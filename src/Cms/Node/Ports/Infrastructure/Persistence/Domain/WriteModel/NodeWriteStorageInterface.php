<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeWriteStorageInterface
{
    public function find(string $id, string $locale): array;

    public function create(array $node): void;

    public function update(array $node): void;

    public function delete(array $node): void;
}
