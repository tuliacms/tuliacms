<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyRoutingStrategyRegistry
{
    /**
     * @var TaxonomyRoutingStrategyInterface[]
     */
    private iterable $strategies = [];

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function has(string $name): bool
    {
        $this->prepare();

        return isset($this->strategies[$name]);
    }

    public function get(string $name): TaxonomyRoutingStrategyInterface
    {
        $this->prepare();

        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        throw new \OutOfBoundsException(sprintf('Taxonomy strategy named "%s" not found.', $name));
    }

    /**
     * @return TaxonomyRoutingStrategyInterface[]
     */
    public function all(): array
    {
        $this->prepare();

        return $this->strategies;
    }

    private function prepare(): void
    {
        $prepared = [];

        foreach ($this->strategies as $strategy) {
            $prepared[$strategy->getName()] = $strategy;
        }

        $this->strategies = $prepared;
    }
}
