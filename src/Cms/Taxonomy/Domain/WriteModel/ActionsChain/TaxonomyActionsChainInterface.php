<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyActionsChainInterface
{
    public function execute(string $name, Taxonomy $taxonomy): void;

    public function addAction(TaxonomyActionInterface $action, string $name, int $priority): void;
}
