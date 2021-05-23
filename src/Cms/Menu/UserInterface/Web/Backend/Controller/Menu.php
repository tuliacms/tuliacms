<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepository;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Menu extends AbstractController
{
    private MenuRepository $repository;

    private DatatableFactory $factory;

    private MenuDatatableFinderInterface $finder;

    public function __construct(
        MenuRepository $repository,
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
    public function create(Request $request): RedirectResponse
    {
        $menu = $this->repository->createNewMenu();
        $menu->setName($request->request->get('name'));

        $this->repository->save($menu);

        $this->setFlash('success', $this->trans('menuCreated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws NotFoundHttpException
     * @CsrfToken(id="menu.edit")
     */
    public function edit(Request $request): RedirectResponse
    {
        try {
            $menu = $this->repository->find($request->request->get('id'));
        } catch (MenuNotFoundException $e) {
            throw $this->createNotFoundException('Menu not found.', $e);
        }

        $menu->setName($request->request->get('name'));

        $this->repository->update($menu);

        $this->setFlash('success', $this->trans('menuUpdated', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="menu.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        foreach ($request->request->get('ids', []) as $id) {
            $menu = $this->repository->find($id);
            $this->repository->delete($menu);
        }

        $this->setFlash('success', $this->trans('selectedMenusWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }
}
