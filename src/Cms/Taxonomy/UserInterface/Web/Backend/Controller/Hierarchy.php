<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Domain\Service\TaxonomyHierarchy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TaxonomyRepository;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    private TaxonomyRepository $repository;

    private TaxonomyHierarchy $hierarchy;

    public function __construct(TaxonomyRepository $repository, TaxonomyHierarchy $hierarchy)
    {
        $this->repository = $repository;
        $this->hierarchy = $hierarchy;
    }

    public function index(string $taxonomyType): ViewInterface
    {
        $taxonomy = $this->repository->get($taxonomyType);
        $terms = iterator_to_array($taxonomy->terms());
        $tree = $this->buildTree(Term::ROOT_ID, $terms);

        return $this->view('@backend/taxonomy/hierarchy/index.tpl', [
            'tree' => $tree,
            'taxonomyType' => $taxonomy->getType(),
        ]);
    }

    /**
     * @CsrfToken(id="taxonomy_hierarchy")
     */
    public function save(Request $request, string $taxonomyType): RedirectResponse
    {
        $taxonomy = $this->repository->get($taxonomyType);
        $hierarchy = $request->request->get('term', []);

        if (empty($hierarchy)) {
            return $this->redirectToRoute('backend.term.hierarchy', ['taxonomyType' => $taxonomyType]);
        }

        $this->hierarchy->updateHierarchy($taxonomy, $hierarchy);

        $this->repository->save($taxonomy);

        $this->setFlash('success', $this->trans('hierarchyUpdated', [], 'taxonomy'));
        return $this->redirectToRoute('backend.term.hierarchy', ['taxonomyType' => $taxonomyType]);
    }

    private function buildTree(?string $parentId, array $terms): array
    {
        $tree = [];

        foreach ($terms as $term) {
            if ($term->getParentId() && $term->getParentId()->getId() === $parentId) {
                $leaf = [
                    'id' => $term->getId()->getId(),
                    'name' => $term->getName(),
                    'position' => $term->getPosition(),
                    'children' => $this->buildTree($term->getId()->getId(), $terms),
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
