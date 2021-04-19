<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Factory;

use Tulia\Cms\SearchAnything\EngineInterface;
use Tulia\Cms\SearchAnything\Engine;
use Tulia\Cms\SearchAnything\Provider\ProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EngineFactory implements EngineFactoryInterface
{
    /**
     * @var ProviderInterface[]
     */
    protected iterable $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function providerEngine(string $provider): EngineInterface
    {
        $providers = $this->getProviders();

        return new Engine([$providers[$provider]]);
    }

    public function getProviders(): iterable
    {
        $providers = [];

        foreach ($this->providers as $provider) {
            $providers[$provider->getId()] = $provider;
        }

        return $providers;
    }
}
