<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
class StrategyRegistry
{
    /**
     * @var array|StrategyInterface[]
     */
    private $strategies;

    /**
     * @param iterable $strategies
     */
    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        $this->prepare();

        return isset($this->strategies[$name]);
    }

    /**
     * @param string $name
     *
     * @return StrategyInterface
     */
    public function get(string $name): StrategyInterface
    {
        $this->prepare();

        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        throw new \OutOfBoundsException(sprintf('Taxonomy strategy named "%s" not found.', $name));
    }

    /**
     * @return array|StrategyInterface[]
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
