<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Persistence\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface QueryInterface
{
    public function findCollection(array $criteria, int $start = 0, int $limit = 10): array;
}
