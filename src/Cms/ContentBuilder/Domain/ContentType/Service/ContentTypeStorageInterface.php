<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeStorageInterface
{
    public function find(string $id): array;

    public function insert(array $nodeType): void;

    public function update(array $nodeType): void;

    public function delete(array $nodeType): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
