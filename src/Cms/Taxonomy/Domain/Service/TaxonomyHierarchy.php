<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Service;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyHierarchy
{
    public function updateHierarchy(Taxonomy $taxonomy, array $hierarchy): void
    {
        $rebuildedHierarchy = [];

        foreach ($hierarchy as $child => $parent) {
            $rebuildedHierarchy[$parent][] = $child;
        }

        foreach ($rebuildedHierarchy as $parent => $terms) {
            foreach ($terms as $level => $id) {
                $term = $taxonomy->getTerm(new TermId($id));
                $term->setParentId(new TermId($parent ?: Term::ROOT_ID));
                $term->setPosition($level + 1);
            }
        }

        $this->calculateLevel($taxonomy, Term::ROOT_ID, 0);
    }

    public function calculateLevel(Taxonomy $taxonomy, string $parentId, int $baseLevel): void
    {
        foreach ($taxonomy->terms() as $term) {
            if ($term->getParentId() && $term->getParentId()->getId() === $parentId) {
                $term->setLevel($baseLevel + 1);

                $this->calculateLevel($taxonomy, $term->getId()->getId(), $baseLevel + 1);
            }
        }
    }
}
