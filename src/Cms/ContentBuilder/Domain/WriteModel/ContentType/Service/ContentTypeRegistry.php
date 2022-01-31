<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecorator;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\ContentTypeNotExistsException;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRegistry
{
    /**
     * @var ContentType[]
     */
    protected array $contentTypes = [];

    /**
     * @var ContentTypeProviderInterface[]
     */
    protected array $nodeTypeProviders = [];

    private ContentTypeDecorator $decorator;

    public function __construct(ContentTypeDecorator $decorator)
    {
        $this->decorator = $decorator;
    }

    public function addProvider(ContentTypeProviderInterface $nodeTypeProvider): void
    {
        $this->nodeTypeProviders[] = $nodeTypeProvider;
    }

    /**
     * @throws ContentTypeNotExistsException
     */
    public function get(string $type): ContentType
    {
        $this->fetch();

        if (isset($this->contentTypes[$type]) === false) {
            throw ContentTypeNotExistsException::fromType($type);
        }

        return $this->contentTypes[$type];
    }

    public function has(string $type): bool
    {
        $this->fetch();

        return isset($this->contentTypes[$type]);
    }

    /**
     * @return ContentType[]
     */
    public function getTypes(): array
    {
        $this->fetch();

        return array_keys($this->contentTypes);
    }

    /**
     * @return ContentType[]
     */
    public function all(): array
    {
        $this->fetch();

        return $this->contentTypes;
    }

    private function fetch(): void
    {
        if ($this->contentTypes !== []) {
            return;
        }

        $types = [];

        foreach ($this->nodeTypeProviders as $provider) {
            $types[] = $provider->provide();
        }

        /** @var ContentType $type */
        foreach (array_merge(...$types) as $type) {
            $this->decorator->decorate($type);
            $type->validate();

            $this->contentTypes[$type->getCode()] = $type;
        }
    }
}
