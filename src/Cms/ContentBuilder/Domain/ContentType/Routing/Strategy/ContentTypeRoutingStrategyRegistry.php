<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Routing\Strategy;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRoutingStrategyRegistry
{
    /**
     * @var ContentTypeRoutingStrategyInterface[]
     */
    private array $strategies = [];

    public function addStrategy(ContentTypeRoutingStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    public function get(string $id, string $contentType): ContentTypeRoutingStrategyInterface
    {
        $this->prepare();

        foreach ($this->strategies as $strategy) {
            if ($strategy->getId() === $id && $strategy->supports($contentType)) {
                return $strategy;
            }
        }

        throw new \OutOfBoundsException(sprintf('Routing strategy with ID "%s" not found.', $id));
    }

    public function getByContentType(ContentType $contentType): ContentTypeRoutingStrategyInterface
    {
        return $this->get($contentType->getRoutingStrategy(), $contentType->getType());
    }

    /**
     * @return ContentTypeRoutingStrategyInterface[]
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
            $prepared[] = $strategy;
        }

        $this->strategies = $prepared;
    }
}
