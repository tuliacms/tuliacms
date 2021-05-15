<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

/**
 * @author Adam Banaszkiewicz
 */
interface CollectionInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * @param $element
     */
    public function append($element): void;

    /**
     * @param CollectionInterface $collection
     */
    public function merge(CollectionInterface $collection): void;

    /**
     * @param array $nodes
     */
    public function replace(array $nodes): void;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return FileInterface|null
     */
    public function first(): ?FileInterface;
}
