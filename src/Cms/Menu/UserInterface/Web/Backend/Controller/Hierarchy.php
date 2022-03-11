<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Menu\Application\UseCase\UpdateMenu;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepositoryInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    private MenuRepositoryInterface $repository;

    public function __construct(MenuRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(string $menuId)
    {
        $menu = $this->repository->find($menuId);

        if (!$menu) {
            $this->setFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        return $this->view('@backend/menu/hierarchy/index.tpl', [
            'menu' => $menu,
            'tree' => $this->buildTree(Item::ROOT_ID, $menu->itemsToArray()),
            'menuId' => $menuId,
        ]);
    }

    /**
     * @CsrfToken(id="menu_hierarchy")
     */
    public function save(Request $request, UpdateMenu $updateMenu, string $menuId): RedirectResponse
    {
        $menu = $this->repository->find($menuId);

        if (!$menu) {
            $this->setFlash('success', $this->trans('menuNotFound', [], 'menu'));
            return $this->redirectToRoute('backend.menu');
        }

        $hierarchy = (array) $request->request->get('term', []);

        if (empty($hierarchy)) {
            return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
        }

        $menu->updateHierarchy($hierarchy);

        ($updateMenu)($menu);

        $this->setFlash('success', $this->trans('hierarchyUpdated'));
        return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
    }

    private function buildTree(?string $parentId, array $items): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $leaf = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'position' => $item['position'],
                    'children' => $this->buildTree($item['id'], $items),
                ];

                $tree[] = $leaf;
            }
        }

        usort($tree, function (array $a, array $b) {
            return $a['position'] <=> $b['position'];
        });

        return $tree;
    }
}
