<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Menu\Domain\Service\MenuHierarchy;
use Tulia\Cms\Menu\Domain\WriteModel\MenuRepository;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    private MenuRepository $repository;

    private MenuHierarchy $hierarchy;

    public function __construct(MenuRepository $repository, MenuHierarchy $hierarchy)
    {
        $this->repository = $repository;
        $this->hierarchy = $hierarchy;
    }

    public function index(string $menuId): ViewInterface
    {
        $menu = $this->repository->find($menuId);
        $items = iterator_to_array($menu->items());
        $tree = $this->buildTree(Item::ROOT_ID, $items);

        return $this->view('@backend/menu/hierarchy/index.tpl', [
            'menu' => $menu,
            'tree' => $tree,
            'menuId' => $menuId,
        ]);
    }

    /**
     * @CsrfToken(id="menu_hierarchy")
     */
    public function save(Request $request, string $menuId): RedirectResponse
    {
        $taxonomy = $this->repository->find($menuId);
        $hierarchy = $request->request->get('term', []);

        if (empty($hierarchy)) {
            return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
        }

        $this->hierarchy->updateHierarchy($taxonomy, $hierarchy);

        $this->repository->update($taxonomy);

        $this->setFlash('success', $this->trans('hierarchyUpdated'));
        return $this->redirectToRoute('backend.menu.item.hierarchy', ['menuId' => $menuId]);
    }

    private function buildTree(?string $parentId, array $terms): array
    {
        $tree = [];

        foreach ($terms as $term) {
            if ($term->getParentId() === $parentId) {
                $leaf = [
                    'id' => $term->getId(),
                    'name' => $term->getName(),
                    'position' => $term->getPosition(),
                    'children' => $this->buildTree($term->getId(), $terms),
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
