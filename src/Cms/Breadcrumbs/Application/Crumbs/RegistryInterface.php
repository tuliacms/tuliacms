<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Application\Crumbs;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return ResolverInterface[]
     */
    public function all(): array;
}
