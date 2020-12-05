<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Query;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    /**
     * @param int $part
     *
     * @return array
     */
    public function findPart(int $part = 1): array;
}
