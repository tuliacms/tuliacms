<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeHandler;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeHandlerRegistry
{
    /**
     * @var FieldTypeHandlerInterface[]
     */
    private array $handlers = [];

    public function addHandler(string $type, FieldTypeHandlerInterface $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    public function has(string $type): bool
    {
        return isset($this->handlers[$type]);
    }

    public function get(string $type): FieldTypeHandlerInterface
    {
        return $this->handlers[$type];
    }

    public function all(): array
    {
        return $this->handlers;
    }
}
