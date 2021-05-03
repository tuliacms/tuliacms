<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Item\DatatableFinder;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\UserInterface\Web\Form\MenuItemForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\DependencyInjection\Exception\MissingServiceException;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    protected MenuRepositoryInterface $repository;
    protected ManagerFactoryInterface $formManager;
    private RegistryInterface $menuTypeRegistry;

    public function __construct(
        MenuRepositoryInterface $repository,
        ManagerFactoryInterface $formManager,
        RegistryInterface $menuTypeRegistry
    ) {
        $this->repository = $repository;
        $this->formManager = $formManager;
        $this->menuTypeRegistry = $menuTypeRegistry;
    }

    public function index(string $menuId): RedirectResponse
    {
        return $this->redirectToRoute('backend.menu.item.list', [
            'menuId' => $menuId,
        ]);
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder, string $menuId)
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $finder->setMenuId($menuId);

        return $this->view('@backend/menu/item/list.tpl', [
            'menu' => $menu,
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder, string $menuId): JsonResponse
    {
        $finder->setMenuId($menuId);
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="menu_item_form")
     */
    public function create(Request $request, string $menuId)
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $item = $this->repository->createNewItem([
            'parent_id' => $request->query->get('parentId'),
        ]);

        $manager = $this->formManager->getInstanceFor($item, \Tulia\Cms\Menu\UserInterface\Web\Form\ScopeEnum::BACKEND_EDIT);
        $form = $manager->createForm(MenuItemForm::class, $item, [
            'persist_mode' => 'create',
            'menu_id' => $menuId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->addItem($form->getData());
            $this->repository->update($menu);

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/create.tpl', [
            'menu'  => $menu,
            'model' => $item,
            'form'  => $form->createView(),
            'types' => $this->collectMenuTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @param string $id
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="menu_item_form")
     */
    public function edit(Request $request, string $menuId, string $id)
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        try {
            $item = $menu->getItem($id);
        } catch (ItemNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuItemNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', ['menuId' => $menuId]);
        }

        $manager = $this->formManager->getInstanceFor($item, \Tulia\Cms\Menu\UserInterface\Web\Form\ScopeEnum::BACKEND_EDIT);
        $form = $manager->createForm(MenuItemForm::class, $item, ['persist_mode' => 'update']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->update($menu);

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'model' => $item,
            'form'  => $form->createView(),
            'types' => $this->collectMenuTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @return RedirectResponse
     * @CsrfToken(id="menu.item.delete")
     */
    public function delete(Request $request, string $menuId): RedirectResponse
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        foreach ($request->request->get('ids', []) as $id) {
            try {
                $menu->removeItem($menu->getItem($id));
                $this->repository->save($menu);
            } catch (ItemNotFoundException $e) {
                // Do nothing when Item not exists.
                continue;
            }
        }

        $this->setFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu.item.list', [ 'menuId' => $menuId ]);
    }

    private function collectMenuTypes(): array
    {
        $types = [];

        foreach ($this->menuTypeRegistry->all() as $type) {
            if (empty($type->getSelectorService())) {
                continue;
            }

            try {
                $types[] = [
                    'type'     => $type,
                    'selector' => $this->container->get($type->getSelectorService()),
                ];
            } catch (MissingServiceException $e) {
                throw new \RuntimeException(sprintf('Cannot load SelectorService for %s type, searching for service named %s.', $type->getType(), $type->getSelectorService()), 0, $e);
            }
        }

        return $types;
    }
}
