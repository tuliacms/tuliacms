<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeWriteStorageInterface
{
    public function find(string $id, string $websiteId, string $locale, string $defaultLocale): array;

    public function insert(array $node, string $defaultLocale): void;

    public function update(array $node, string $defaultLocale): void;

    public function delete(array $node): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
