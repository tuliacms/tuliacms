<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface CollectionInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * @return Website[]
     */
    public function all(): array;

    public function append(Website $element): void;

    public function merge(CollectionInterface $collection): void;

    public function replace(array $elements): void;

    public function count(): int;

    public function first(): ?Website;

    public function toArray(): array;
}
