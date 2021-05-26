<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepository;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Cms\Menu\UserInterface\Web\Backend\Form\MenuItemForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\DependencyInjection\Exception\MissingServiceException;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    private MenuRepository $repository;

    private RegistryInterface $menuTypeRegistry;

    private DatatableFactory $factory;

    private ItemDatatableFinderInterface $finder;

    public function __construct(
        MenuRepository $repository,
        RegistryInterface $menuTypeRegistry,
        DatatableFactory $factory,
        ItemDatatableFinderInterface $finder
    ) {
        $this->repository = $repository;
        $this->menuTypeRegistry = $menuTypeRegistry;
        $this->factory = $factory;
        $this->finder = $finder;
    }

    public function index(string $menuId): RedirectResponse
    {
        return $this->redirectToRoute('backend.menu.item.list', [
            'menuId' => $menuId,
        ]);
    }

    public function list(Request $request, string $menuId)
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $this->finder->setMenuId($menuId);

        return $this->view('@backend/menu/item/list.tpl', [
            'menu' => $menu,
            'datatable' => $this->factory->create($this->finder, $request),
        ]);
    }

    public function datatable(Request $request, string $menuId): JsonResponse
    {
        $this->finder->setMenuId($menuId);
        return $this->factory->create($this->finder, $request)->generateResponse();
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

        $item = $this->repository->createNewItem();
        $item->setParentId($request->query->get('parentId'));

        $form = $this->createForm(MenuItemForm::class, $item, [
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
            'item'  => $item,
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

        $form = $this->createForm(MenuItemForm::class, $item, [
            'persist_mode' => 'update',
            'menu_id' => $menuId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->update($menu);

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'item'  => $item,
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
            } catch (ItemNotFoundException $e) {
                // Do nothing when Item not exists.
                continue;
            }
        }

        $this->repository->update($menu);

        $this->setFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu.item.list', [ 'menuId' => $menuId ]);
    }

    private function collectMenuTypes(): array
    {
        $types = [];

        foreach ($this->menuTypeRegistry->all() as $type) {
            if ($type->getSelectorService() === null) {
                continue;
            }

            try {
                $types[] = [
                    'type'     => $type,
                    'selector' => $type->getSelectorService(),
                ];
            } catch (MissingServiceException $e) {
                throw new \RuntimeException(sprintf('Cannot load SelectorService for %s type, searching for service named %s.', $type->getType(), $type->getSelectorService()), 0, $e);
            }
        }

        return $types;
    }
}
