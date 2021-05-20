<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Taxonomy\Domain\Service\TaxonomyGlobalOrderRecalculator;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
class GlobalOrderGenerator implements TaxonomyActionInterface
{
    private TaxonomyGlobalOrderRecalculator $recalculator;

    public function __construct(TaxonomyGlobalOrderRecalculator $recalculator)
    {
        $this->recalculator = $recalculator;
    }

    public static function supports(): array
    {
        return ['save' => 100];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        $this->recalculator->recalculate($taxonomy);
    }
}
