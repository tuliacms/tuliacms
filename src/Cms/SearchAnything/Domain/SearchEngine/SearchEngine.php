<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Domain\SearchEngine;

use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Tulia\Cms\SearchAnything\Ports\Provider\ProviderInterface;
use Tulia\Cms\SearchAnything\Ports\SearchEngine\SearchEngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchEngine implements SearchEngineInterface
{
    /**
     * @var ProviderInterface[]
     */
    protected iterable $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return iterator_to_array($this->providers);
    }

    public function searchInProvider(string $providerName, string $query, int $limit = 5, int $page = 1): Results
    {
        $provider = null;

        foreach ($this->providers as $pretendent) {
            if ($pretendent->getId() === $providerName) {
                $provider = $pretendent;
            }
        }

        $results = $provider->search($query, $limit, $page);
        $results->setLabel($provider->getLabel());
        $results->setIcon($provider->getIcon());

        return $results;
    }
}
