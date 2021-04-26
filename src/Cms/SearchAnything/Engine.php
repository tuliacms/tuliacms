<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything;

use Tulia\Cms\SearchAnything\Provider\ProviderInterface;
use Tulia\Cms\SearchAnything\Results\ResultsInterface;
use Tulia\Cms\SearchAnything\Results\Results;

/**
 * @author Adam Banaszkiewicz
 */
class Engine implements EngineInterface
{
    /**
     * @var iterable
     */
    protected $providers;

    /**
     * @param iterable $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function getProviders(): iterable
    {
        return $this->providers;
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $page
     *
     * @return ResultsInterface
     *
     * @throws \Exception
     */
    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $provider = reset($this->providers);
        $results = $provider->search($query, $limit, $page);
        $results->setLabel($provider->getLabel());
        $results->setIcon($provider->getIcon());

        return $results;
    }
}
