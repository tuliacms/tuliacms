<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\NodeTypeNotExistsException;
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

    private NodeTypeDecorator $decorator;

    public function __construct(NodeTypeDecorator $decorator)
    {
        $this->decorator = $decorator;
    }

    public function addProvider(NodeTypeProviderInterface $nodeTypeProvider): void
    {
        $this->nodeTypeProviders[] = $nodeTypeProvider;
    }

    /**
     * @throws NodeTypeNotExistsException
     */
    public function get(string $type): NodeType
    {
        $this->fetch();

        if (isset($this->nodeTypes[$type]) === false) {
            throw NodeTypeNotExistsException::fromType($type);
        }

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

    /**
     * @return NodeType[]
     */
    public function all(): array
    {
        $this->fetch();

        return $this->nodeTypes;
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

        /** @var NodeType $type */
        foreach (array_merge(...$types) as $type) {
            $this->decorator->decorate($type);
            $type->validate();

            $this->nodeTypes[$type->getCode()] = $type;
        }
    }
}
