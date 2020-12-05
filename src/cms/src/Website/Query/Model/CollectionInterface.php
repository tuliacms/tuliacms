<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface CollectionInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * @return array
     */
    public function all(): array;

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
     * @return Website|null
     */
    public function first(): ?Website;

    /**
     * @return array
     */
    public function toArray(): array;
}
