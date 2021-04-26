<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

use Tulia\Cms\Menu\Application\Query\Finder\Model\ItemCollection;

/**
 * @author Adam Banaszkiewicz
 */
interface HierarchyBuilderInterface
{
    /**
     * @param string $id
     * @param ItemCollection|null $collection
     *
     * @return HierarchyInterface
     */
    public function build(string $id, ItemCollection $collection = null): HierarchyInterface;
}
