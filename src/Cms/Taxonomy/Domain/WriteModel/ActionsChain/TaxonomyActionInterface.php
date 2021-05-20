<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyActionInterface
{
    public static function supports(): array;

    public function execute(Taxonomy $taxonomy): void;
}
