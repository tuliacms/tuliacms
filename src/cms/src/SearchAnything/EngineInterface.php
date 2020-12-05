<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything;

use Tulia\Cms\SearchAnything\Results\ResultsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface EngineInterface
{
    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface;
}
