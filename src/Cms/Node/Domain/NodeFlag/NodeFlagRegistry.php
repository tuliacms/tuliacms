<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

use Tulia\Cms\Node\Domain\NodeFlag\Exception\FlagNotFoundException;

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

    public function isSingular(string $name): bool
    {
        $this->resolveFlags();

        foreach ($this->flags as $key => $flag) {
            if ($key === $name) {
                return (bool) $flag['singular'];
            }
        }

        throw FlagNotFoundException::fromName($name);
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
