<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

/**
 * @author Adam Banaszkiewicz
 */
interface HierarchyBuilderInterface
{
    public function build(string $id, array $collection = []): HierarchyInterface;
}
