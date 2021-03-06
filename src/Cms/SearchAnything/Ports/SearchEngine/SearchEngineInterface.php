<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Ports\SearchEngine;

use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Tulia\Cms\SearchAnything\Ports\Provider\ProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SearchEngineInterface
{
    /**
     * @return ProviderInterface[]
     */
    public function getProviders(): array;

    public function searchInProvider(string $providerName, string $query, int $limit = 5, int $page = 1): Results;
}
