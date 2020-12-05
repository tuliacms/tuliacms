<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Links;

/**
 * @author Adam Banaszkiewicz
 */
class ProviderRegistry
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return ProviderInterface[]
     */
    public function all(): array
    {
        return iterator_to_array($this->providers);
    }
}
