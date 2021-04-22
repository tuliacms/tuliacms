<?php

namespace Tulia\Cms\Metadata\Registrator;

interface ContentFieldsInterface extends \ArrayAccess, \IteratorAggregate
{
    public function add(array $field): self;
    public function remove(string $field): self;
    public function empty(): self;
    public function count(): int;
    public function getNames(): array;
    public function all(): array;
}