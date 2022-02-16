<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeBuilderRegistry
{
    /**
     * @var FieldTypeBuilderInterface[]
     */
    private array $builders = [];

    public function addBuilder(string $type, FieldTypeBuilderInterface $builder): void
    {
        $this->builders[$type] = $builder;
    }

    public function has(string $type): bool
    {
        return isset($this->builders[$type]);
    }

    public function get(string $type): FieldTypeBuilderInterface
    {
        return $this->builders[$type];
    }

    public function all(): array
    {
        return $this->builders;
    }
}
