<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Persistence\Command;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    public function save(array $data): void;
    public function delete(string $id): void;
}
