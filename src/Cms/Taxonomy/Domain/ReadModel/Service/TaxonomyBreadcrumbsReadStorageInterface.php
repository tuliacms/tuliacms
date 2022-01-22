<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyBreadcrumbsReadStorageInterface
{
    public function find(string $termId, string $websiteId, string $locale, string $defaultLocale): array;
}
