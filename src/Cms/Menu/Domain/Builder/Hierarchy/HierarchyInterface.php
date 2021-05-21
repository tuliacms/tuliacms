<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Hierarchy;

/**
 * @author Adam Banaszkiewicz
 */
interface HierarchyInterface extends \ArrayAccess, \IteratorAggregate
{
    public function getId(): string;

    public function append(Item $item): void;

    public function flatten(): HierarchyInterface;
}
