<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface;

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
