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

    /**
     * @var NodeTypeDecoratorInterface[]
     */
    protected array $decorators = [];

    public function addDecorator(NodeTypeDecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
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
            $this->decorate($type);
            $this->nodeTypes[$type->getType()] = $type;
        }
    }

    private function decorate(NodeType $nodeType): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($nodeType);
        }
    }
}
