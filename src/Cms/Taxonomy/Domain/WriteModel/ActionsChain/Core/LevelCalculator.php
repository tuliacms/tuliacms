<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
class LevelCalculator implements TaxonomyActionInterface
{
    public static function supports(): array
    {
        return [
            'save' => 100,
        ];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        foreach ($taxonomy->terms() as $term) {
            if ($term->getParentId() === null) {
                $term->setLevel(0);
            } else {
                $parent = $taxonomy->getTerm($term->getParentId());

                $term->setLevel($parent->getLevel() + 1);
            }
        }
    }
}
