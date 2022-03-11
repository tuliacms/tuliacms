<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenu;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenuItem;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\ItemNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    private MenuRepositoryInterface $repository;
    private RegistryInterface $menuTypeRegistry;
    private DatatableFactory $factory;
    private ItemDatatableFinderInterface $finder;
    private ContentFormService $contentFormService;

    public function __construct(
        MenuRepositoryInterface $repository,
        RegistryInterface $menuTypeRegistry,
        DatatableFactory $factory,
        ItemDatatableFinderInterface $finder,
        ContentFormService $contentFormService
    ) {
        $this->repository = $repository;
        $this->menuTypeRegistry = $menuTypeRegistry;
        $this->factory = $factory;
        $this->finder = $finder;
        $this->contentFormService = $contentFormService;
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
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_menu_item")
     */
    public function create(Request $request, UpdateMenuItem $updateMenuItem, string $menuId)
    {
        try {
            $menu = $this->repository->find($menuId);
        } catch (MenuNotFoundException $e) {
            $this->setFlash('danger', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $item = $this->repository->createNewItem($menu);
        $item->setParentId($request->query->get('parentId', Item::ROOT_ID));

        $formDescriptor = $this->produceFormDescriptor($item);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateMenuItem)($menu, $item, $formDescriptor->getData());

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/create.tpl', [
            'menu'  => $menu,
            'item'  => $item,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_menu_item")
     */
    public function edit(Request $request, UpdateMenuItem $updateMenuItem, string $menuId, string $id)
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

        $formDescriptor = $this->produceFormDescriptor($item);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateMenuItem)($menu, $item, $formDescriptor->getData());

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirectToRoute('backend.menu.item', [ 'menuId' => $menu->getId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'item'  => $item,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @return RedirectResponse
     * @CsrfToken(id="menu.item.delete")
     */
    public function delete(Request $request, UpdateMenu $updateMenu, string $menuId): RedirectResponse
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

        ($updateMenu)($menu);

        $this->setFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu.item.list', [ 'menuId' => $menuId ]);
    }

    private function produceFormDescriptor(Item $menuItem): ContentTypeFormDescriptor
    {
        return $this->contentFormService->buildFormDescriptor('menu_item', $menuItem->toArray(), [
            'item' => $menuItem,
            'types' => $this->collectMenuTypes(),
        ]);
    }

    private function collectMenuTypes(): array
    {
        $types = [];

        foreach ($this->menuTypeRegistry->all() as $type) {
            if ($type->getSelectorService() === null) {
                continue;
            }

            $types[] = [
                'type'     => $type,
                'selector' => $type->getSelectorService(),
            ];
        }

        return $types;
    }
}
