<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeRegistry
{
    /**
     * @var NodeType[]
     */
    protected array $nodeTypes = [];

    /**
     * @var NodeTypeProviderInterface[]
     */
    protected array $nodeTypeProviders = [];

    public function addProvider(NodeTypeProviderInterface $nodeTypeProvider): void
    {
        $this->nodeTypeProviders[] = $nodeTypeProvider;
    }

    public function get(string $type): NodeType
    {
        $this->fetch();

        return $this->nodeTypes[$type];
    }

    public function has(string $type): bool
    {
        $this->fetch();

        return isset($this->nodeTypes[$type]);
    }

    public function getTypes(): array
    {
        $this->fetch();

        return array_keys($this->nodeTypes);
    }

    private function fetch(): void
    {
        if ($this->nodeTypes !== []) {
            return;
        }

        $types = [];

        foreach ($this->nodeTypeProviders as $provider) {
            $types[] = $provider->provide();
        }

        foreach (array_merge(...$types) as $type) {
            $this->nodeTypes[$type->getName()] = $type;
        }
    }
}
