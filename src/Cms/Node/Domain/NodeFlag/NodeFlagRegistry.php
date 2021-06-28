<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFlagRegistry implements NodeFlagRegistryInterface
{
    /**
     * @var NodeFlagProviderInterface[]
     */
    private iterable $providers;

    private array $flags = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function all(): array
    {
        $this->resolveFlags();

        return $this->flags;
    }

    private function resolveFlags(): void
    {
        if ($this->flags !== []) {
            return;
        }

        foreach ($this->providers as $provider) {
            $this->flags[] = $provider->provide();
        }

        $this->flags = array_merge(...$this->flags);

        foreach ($this->flags as $type => $flag) {
            $this->flags[$type] = array_merge([
                'singular' => false,
                'label' => 'flagType.' . $type,
            ], $this->flags[$type]);
        }
    }
}
