<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\ContentTypeNotExistsException;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRegistry implements ContentTypeRegistryInterface
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

    public function __construct(
        ContentTypeDecorator $decorator
    ) {
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
    public function all(): \Traversable
    {
        $this->fetch();

        foreach ($this->contentTypes as $contentType) {
            yield $contentType;
        }
    }

    /**
     * @return ContentType[]
     */
    public function allByType(string $type): \Traversable
    {
        $this->fetch();

        foreach ($this->contentTypes as $contentType) {
            if ($contentType->getType() === $type) {
                yield $contentType;
            }
        }
    }

    protected function fetch(): array
    {
        if ($this->contentTypes !== []) {
            return [];
        }

        $types = [];

        foreach ($this->nodeTypeProviders as $provider) {
            $types[] = $provider->provide();
        }

        $this->contentTypes = [];

        /** @var ContentType $type */
        foreach (array_merge(...$types) as $type) {
            $this->decorator->decorate($type);

            $this->contentTypes[$type->getCode()] = $type;
        }

        return $this->contentTypes;
    }
}
