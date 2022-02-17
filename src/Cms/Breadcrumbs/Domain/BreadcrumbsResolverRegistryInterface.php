<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

/**
 * @author Adam Banaszkiewicz
 */
interface BreadcrumbsResolverRegistryInterface
{
    /**
     * @return BreadcrumbsResolverInterface[]
     */
    public function all(): array;
}
