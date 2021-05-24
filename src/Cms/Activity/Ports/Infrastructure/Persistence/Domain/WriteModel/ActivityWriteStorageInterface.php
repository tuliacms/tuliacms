<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface ActivityWriteStorageInterface
{
    public function save(array $data): void;

    public function delete(string $id): void;
}
