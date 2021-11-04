<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Exception\TaxonomyTypeNotExistsException;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeRegistry
{
    /**
     * @var TaxonomyType[]
     */
    protected array $nodeTypes = [];

    /**
     * @var TaxonomyTypeProviderInterface[]
     */
    protected array $nodeTypeProviders = [];

    /**
     * @var TaxonomyTypeDecoratorInterface[]
     */
    protected array $decorators = [];

    public function addDecorator(TaxonomyTypeDecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
    }

    public function addProvider(TaxonomyTypeProviderInterface $nodeTypeProvider): void
    {
        $this->nodeTypeProviders[] = $nodeTypeProvider;
    }

    /**
     * @throws TaxonomyTypeNotExistsException
     */
    public function get(string $type): TaxonomyType
    {
        $this->fetch();

        if (isset($this->nodeTypes[$type]) === false) {
            throw TaxonomyTypeNotExistsException::fromType($type);
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
     * @return TaxonomyType[]
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

        /** @var TaxonomyType $type */
        foreach (array_merge(...$types) as $type) {
            $this->decorate($type);
            $this->nodeTypes[$type->getType()] = $type;
        }
    }

    private function decorate(TaxonomyType $nodeType): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($nodeType);
        }
    }
}
