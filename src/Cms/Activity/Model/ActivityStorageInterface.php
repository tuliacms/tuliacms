<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface ActivityStorageInterface
{
    public function store(ActivityRow $activityRow): void;

    public function delete(ActivityRow $activityRow): void;

    public function findCollection(int $start = 0, int $limit = 10): array;
}
