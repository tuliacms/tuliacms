<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutTypeBuilderRegistry
{
    /**
     * @var LayoutTypeBuilderInterface[]
     */
    protected array $builders = [];

    public function addBuilder(LayoutTypeBuilderInterface $builder): void
    {
        $this->builders[get_class($builder)] = $builder;
    }

    public function has(string $name): bool
    {
        return isset($this->builders[$name]);
    }

    public function get(string $name): LayoutTypeBuilderInterface
    {
        return $this->builders[$name];
    }
}
