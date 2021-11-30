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
    protected array $taxonomyTypes = [];

    /**
     * @var TaxonomyTypeProviderInterface[]
     */
    protected array $taxonomyTypeProviders = [];

    /**
     * @var TaxonomyTypeDecoratorInterface[]
     */
    protected array $decorators = [];

    public function addDecorator(TaxonomyTypeDecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
    }

    public function addProvider(TaxonomyTypeProviderInterface $taxonomyTypeProvider): void
    {
        $this->taxonomyTypeProviders[] = $taxonomyTypeProvider;
    }

    /**
     * @throws TaxonomyTypeNotExistsException
     */
    public function get(string $type): TaxonomyType
    {
        $this->fetch();

        if (isset($this->taxonomyTypes[$type]) === false) {
            throw TaxonomyTypeNotExistsException::fromType($type);
        }

        return $this->taxonomyTypes[$type];
    }

    public function has(string $type): bool
    {
        $this->fetch();

        return isset($this->taxonomyTypes[$type]);
    }

    public function getTypes(): array
    {
        $this->fetch();

        return array_keys($this->taxonomyTypes);
    }

    /**
     * @return TaxonomyType[]
     */
    public function all(): array
    {
        $this->fetch();

        return $this->taxonomyTypes;
    }

    private function fetch(): void
    {
        if ($this->taxonomyTypes !== []) {
            return;
        }

        $types = [];

        foreach ($this->taxonomyTypeProviders as $provider) {
            $types[] = $provider->provide();
        }

        /** @var TaxonomyType $type */
        foreach (array_merge(...$types) as $type) {
            $this->decorate($type);
            $this->taxonomyTypes[$type->getType()] = $type;
        }
    }

    private function decorate(TaxonomyType $taxonomyType): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($taxonomyType);
        }
    }
}
