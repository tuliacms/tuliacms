<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

/**
 * @author Adam Banaszkiewicz
 */
interface HierarchyInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param Item $item
     */
    public function append(Item $item): void;

    /**
     * @return HierarchyInterface
     */
    public function flatten(): HierarchyInterface;
}
