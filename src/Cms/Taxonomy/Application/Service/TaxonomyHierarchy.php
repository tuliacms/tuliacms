<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Service;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

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
                $term = $taxonomy->getTerm($id);
                $term->setParentId($parent ?: null);
                $term->setPosition($level + 1);
            }
        }
    }
}
