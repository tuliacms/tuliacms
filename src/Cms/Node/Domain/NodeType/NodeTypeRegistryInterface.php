<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeTypeRegistryInterface
{
    /**
     * @return NodeTypeInterface[]
     */
    public function all(): iterable;

    public function registerType(string $type): NodeTypeInterface;

    public function getType(string $type): NodeTypeInterface;

    public function isTypeRegistered(string $type): bool;

    public function getRegisteredTypesNames(): iterable;
}
