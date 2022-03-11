<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Menu\Application\UseCase\CreateMenu;
use Tulia\Cms\Menu\Application\UseCase\DeleteMenu;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenu;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Menu extends AbstractController
{
    private MenuRepositoryInterface $repository;
    private DatatableFactory $factory;
    private MenuDatatableFinderInterface $finder;

    public function __construct(
        MenuRepositoryInterface $repository,
        DatatableFactory $factory,
        MenuDatatableFinderInterface $finder
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->finder = $finder;
    }

    public function list(Request $request): ViewInterface
    {
        return $this->view('@backend/menu/menu/list.tpl', [
            'datatable' => $this->factory->create($this->finder, $request),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return $this->factory->create($this->finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="menu.create")
     */
    public function create(Request $request, CreateMenu $createMenu): RedirectResponse
    {
        $menu = $this->repository->createNewMenu();
        $menu->rename($request->request->get('name'));

        ($createMenu)($menu);

        $this->setFlash('success', $this->trans('menuCreated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws NotFoundHttpException
     * @CsrfToken(id="menu.edit")
     */
    public function edit(Request $request, UpdateMenu $updateMenu): RedirectResponse
    {
        $menu = $this->repository->find($request->request->get('id'));

        if (!$menu) {
            $this->setFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $menu->rename($request->request->get('name'));

        ($updateMenu)($menu);

        $this->setFlash('success', $this->trans('menuUpdated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="menu.delete")
     */
    public function delete(Request $request, DeleteMenu $deleteMenu): RedirectResponse
    {
        foreach ($request->request->get('ids', []) as $id) {
            $menu = $this->repository->find($id);

            if ($menu) {
                ($deleteMenu)($menu);
            }
        }

        $this->setFlash('success', $this->trans('selectedMenusWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }
}
