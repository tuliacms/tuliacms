<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Domain;

/**
 * @author Adam Banaszkiewicz
 */
class BodyClassCollection
{
    protected array $classes = [];

    public function add(...$classes): void
    {
        foreach ($classes as $name) {
            $this->classes[$name] = $name;
        }
    }

    public function remove(string $name): void
    {
        unset($this->classes[$name]);
    }

    public function getAll(): array
    {
        return $this->classes;
    }
}
