<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteStorageInterface
{
    public function find(string $id): ?array;
    public function insert(array $website): void;
    public function update(array $website): void;
    public function delete(string $id): void;
}
