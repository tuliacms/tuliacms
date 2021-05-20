<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyRoutingStrategyInterface
{
    public function generate(string $id, string $locale, string $defaultLocale): string;

    public function getName(): string;
}
