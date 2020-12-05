<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Application\Registry;

/**
 * @author Adam Banaszkiewicz
 */
interface ItemRegistryInterface
{
    public function add(string $id, array $item): void;
    public function remove(string $id): void;
    public function get(string $id): ?array;
    public function has(string $id): bool;
    public function all(): array;
    public function replace(array $items): void;
}
