<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyRoutingStrategyInterface
{
    public function generate(string $id, string $locale, string $defaultLocale): string;

    public function generateFromTaxonomy(Taxonomy $taxonomy, string $id): string;

    public function getName(): string;
}
