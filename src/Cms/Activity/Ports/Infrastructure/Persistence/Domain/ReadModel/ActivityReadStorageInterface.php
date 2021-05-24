<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
interface ActivityReadStorageInterface
{
    public function findCollection(array $criteria, int $start = 0, int $limit = 10): array;
}
