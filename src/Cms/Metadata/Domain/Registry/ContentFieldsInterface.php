<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\Registry;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentFieldsInterface extends \ArrayAccess, \IteratorAggregate
{
    public function add(array $field): self;

    public function remove(string $field): self;

    public function empty(): self;

    public function count(): int;

    public function getNames(): array;

    public function all(): array;
}
