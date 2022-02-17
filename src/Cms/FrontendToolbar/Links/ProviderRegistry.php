<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Links;

/**
 * @author Adam Banaszkiewicz
 */
class ProviderRegistry
{
    /**
     * @var LinksCollectorInterface[]
     */
    private iterable $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return LinksCollectorInterface[]
     */
    public function all(): array
    {
        return iterator_to_array($this->providers);
    }
}
