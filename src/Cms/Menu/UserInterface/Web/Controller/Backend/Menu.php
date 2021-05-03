<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Menu\DatatableFinder;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Menu extends AbstractController
{
    protected FinderFactoryInterface $finderFactory;
    protected MenuRepositoryInterface $repository;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        MenuRepositoryInterface $repository
    ) {
        $this->finderFactory = $finderFactory;
        $this->repository = $repository;
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/menu/menu/list.tpl', [
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="menu.create")
     */
    public function create(Request $request): RedirectResponse
    {
        $menu = $this->repository->createNewMenu([
            'name' => $request->request->get('name')
        ]);

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
     * @throws QueryException
     * @CsrfToken(id="menu.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);

        foreach ($request->request->get('ids', []) as $id) {
            $menu = $finder->find($id);

            if ($menu) {
                $this->menuStorage->delete(ApplicationModelMenu::fromQueryModel($menu));
            }
        }

        $this->setFlash('success', $this->trans('selectedMenusWereDeleted', [], 'menu'));
        return $this->redirectToRoute('backend.menu');
    }
}
