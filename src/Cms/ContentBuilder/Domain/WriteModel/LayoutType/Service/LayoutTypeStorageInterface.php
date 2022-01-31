<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\LayoutType\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeStorageInterface
{
    public function find(string $id): array;

    public function insert(array $layoutType): void;

    public function update(array $layoutType): void;

    public function delete(array $layoutType): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
