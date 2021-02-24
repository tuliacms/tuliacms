<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UI\Web\Controller\Backend;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Menu\Application\Command\MenuStorage;
use Tulia\Cms\Menu\Application\Model\Item as ApplicationItem;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Item\DatatableFinder;
use Tulia\Cms\Menu\Application\Query\Finder\Factory\MenuFactoryInterface;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Cms\Menu\UI\Web\Form\MenuItemFormManagerFactory;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\DependencyInjection\Exception\MissingServiceException;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItem extends AbstractController
{
    /**
     * @var FinderFactoryInterface
     */
    protected $menuFinderFactory;

    /**
     * @var MenuStorage
     */
    protected $menuStorage;

    /**
     * @var RegistryInterface
     */
    protected $menuTypeRegistry;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param FinderFactoryInterface $menuFinderFactory
     * @param MenuStorage $menuStorage
     * @param RegistryInterface $menuTypeRegistry
     * @param ContainerInterface $container
     */
    public function __construct(
        FinderFactoryInterface $menuFinderFactory,
        MenuStorage $menuStorage,
        RegistryInterface $menuTypeRegistry,
        ContainerInterface $container
    ) {
        $this->menuFinderFactory = $menuFinderFactory;
        $this->menuStorage       = $menuStorage;
        $this->menuTypeRegistry  = $menuTypeRegistry;
        $this->container         = $container;
    }

    /**
     * @param string $menuId
     *
     * @return RedirectResponse
     */
    public function index(string $menuId): RedirectResponse
    {
        return $this->redirect('backend.menu.item.list', [
            'menuId' => $menuId,
        ]);
    }

    /**
     * @param Request $request
     * @param DatatableFactory $factory
     * @param DatatableFinder $finder
     * @param string $menuId
     *
     * @return ViewInterface
     *
     * @throws NotFoundHttpException
     * @throws QueryException
     */
    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder, string $menuId): ViewInterface
    {
        $menu = $this->findMenu($menuId);
        $finder->setMenuId($menu->getId());

        return $this->view('@backend/menu/item/list.tpl', [
            'menu'      => $menu,
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    /**
     * @param Request $request
     * @param DatatableFactory $factory
     * @param DatatableFinder $finder
     * @param string $menuId
     *
     * @return JsonResponse
     *
     * @throws NotFoundHttpException
     * @throws QueryException
     */
    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder, string $menuId): JsonResponse
    {
        $menu = $this->findMenu($menuId);
        $finder->setMenuId($menu->getId());

        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @param MenuFactoryInterface $itemFactory
     * @param MenuItemFormManagerFactory $managerFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws NotFoundHttpException
     * @throws QueryException
     *
     * @CsrfToken(id="menu_item_form")
     */
    public function create(
        Request $request,
        string $menuId,
        MenuFactoryInterface $itemFactory,
        MenuItemFormManagerFactory $managerFactory
    ) {
        $model = $itemFactory->createNewItem([
            'menu_id'   => $menuId,
            'parent_id' => $request->query->get('parentId'),
        ]);

        $manager = $managerFactory->create($model);
        $form = $manager->createCreateForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirect('backend.menu.item', [ 'menuId' => $model->getMenuId() ]);
        }

        return $this->view('@backend/menu/item/create.tpl', [
            'menu'  => $this->findMenu($menuId),
            'model' => $model,
            'form'  => $form->createView(),
            'types' => $this->collectMenuTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $menuId
     * @param string $id
     * @param MenuItemFormManagerFactory $managerFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws NotFoundHttpException
     * @throws QueryException
     *
     * @CsrfToken(id="menu_item_form")
     */
    public function edit(
        Request $request,
        string $menuId,
        string $id,
        MenuItemFormManagerFactory $managerFactory
    ) {
        $menu  = $this->findMenu($menuId);
        $model = $this->getMenuItem($menu, $id);

        $manager = $managerFactory->create($model);
        $form = $manager->createEditForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('itemSaved', [], 'menu'));
            return $this->redirect('backend.menu.item', [ 'menuId' => $model->getMenuId() ]);
        }

        return $this->view('@backend/menu/item/edit.tpl', [
            'menu'  => $menu,
            'model' => $model,
            'form'  => $form->createView(),
            'types' => $this->collectMenuTypes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $menuId
     *
     * @return RedirectResponse
     *
     * @throws QueryException
     *
     * @CsrfToken(id="menu.item.delete")
     */
    public function delete(Request $request, string $menuId): RedirectResponse
    {
        foreach ($request->request->get('ids', []) as $id) {
            try {
                $item = $this->findItem($id);
                $this->menuStorage->delete(ApplicationItem::fromQueryModel($item));
            } catch (NotFoundHttpException $e) {
                continue;
            }
        }

        $this->setFlash('success', $this->trans('selectedItemsWereDeleted', [], 'menu'));
        return $this->redirect('backend.menu.item.list', [ 'menuId' => $menuId ]);
    }

    /**
     * @param string|null $id
     *
     * @return Menu
     *
     * @throws QueryException
     * @throws NotFoundHttpException
     */
    private function findMenu(?string $id): Menu
    {
        $menu = $this->menuFinderFactory->getInstance(ScopeEnum::BACKEND_SINGLE)->find($id, ['visibility' => null]);

        if (!$menu) {
            throw $this->createNotFoundException('Menu not found');
        }

        return $menu;
    }

    /**
     * @param Menu $menu
     * @param string $itemId
     *
     * @return Item
     */
    private function getMenuItem(Menu $menu, string $itemId): Item
    {
        foreach ($menu->getItems() as $item) {
            if ($item->getId() === $itemId) {
                return $item;
            }
        }

        throw $this->createNotFoundException('Menu not found');
    }

    /**
     * @param string $id
     *
     * @return Item
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function findItem(string $id): Item
    {
        $finder = $this->itemFinderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria([
            'id'       => $id,
            'per_page' => 1,
        ]);
        $finder->fetchRaw();
        $item = $finder->getResult()->first();

        if (!$item) {
            throw $this->createNotFoundException('Menu item not found.');
        }

        return $item;
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
