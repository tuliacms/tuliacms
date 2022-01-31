<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeStorageInterface
{
    public function find(string $id): array;

    public function insert(array $contentType): void;

    public function update(array $contentType): void;

    public function delete(array $contentType): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
