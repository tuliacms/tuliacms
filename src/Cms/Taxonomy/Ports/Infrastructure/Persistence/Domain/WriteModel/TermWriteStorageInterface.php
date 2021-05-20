<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface TermWriteStorageInterface
{
    public function findByType(string $type, string $locale, string $defaultLocale): array;

    public function find(string $id, string $locale, string $defaultLocale): array;

    public function insert(array $term, string $defaultLocale): void;

    public function update(array $term, string $defaultLocale): void;

    public function delete(array $term): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
