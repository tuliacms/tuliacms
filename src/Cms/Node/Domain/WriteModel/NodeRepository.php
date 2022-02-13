<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\ContentTypeNotExistsException;
use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionsChainInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeWriteStorageInterface;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeRepository
{
    private const RESERVED_NAMES = ['title', 'slug', 'parent_id', 'published_at', 'published_to', 'status', 'author_id'];

    private NodeWriteStorageInterface $storage;
    private CurrentWebsiteInterface $currentWebsite;
    private AttributesRepository $metadataRepository;
    private UuidGeneratorInterface $uuidGenerator;
    private EventBusInterface $eventBus;
    private NodeActionsChainInterface $actionsChain;
    private ContentTypeRegistry $contentTypeRegistry;

    public function __construct(
        NodeWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        AttributesRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        NodeActionsChainInterface $actionsChain,
        ContentTypeRegistry $contentTypeRegistry
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function createNew(string $nodeType): Node
    {
        $this->contentTypeRegistry->get($nodeType);

        return Node::createNew(
            $this->uuidGenerator->generate(),
            $nodeType,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );
    }

    /**
     * @throws NodeNotFoundException
     * @throws ContentTypeNotExistsException
     * @throws \Exception
     */
    public function find(string $id): Node
    {
        $node = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if (empty($node)) {
            throw new NodeNotFoundException();
        }

        $nodeType = $this->contentTypeRegistry->get($node['type']);

        $attributesInfo = $this->buildAttributesMapping($nodeType->getFields());

        $attributes = $this->metadataRepository->findAll('node', $id, $attributesInfo);

        $node = Node::buildFromArray($node['type'], [
            'id'            => $node['id'],
            'website_id'    => $node['website_id'],
            'published_at'  => new ImmutableDateTime($node['published_at']),
            'published_to'  => $node['published_to'] ? new ImmutableDateTime($node['published_to']) : null,
            'created_at'    => new ImmutableDateTime($node['created_at']),
            'updated_at'    => $node['updated_at'] ? new ImmutableDateTime($node['updated_at']) : null,
            'status'        => $node['status'] ?? '',
            'author_id'     => $node['author_id'],
            'level'         => (int) $node['level'],
            'parent_id'     => $node['parent_id'] ?? null,
            'locale'        => $node['locale'],
            'translated'    => $node['translated'] ?? true,
            'title'         => $node['title'],
            'slug'          => $node['slug'],
            'attributes'    => $attributes,
        ]);

        $this->actionsChain->execute('find', $node);

        return $node;
    }

    public function insert(Node $node): void
    {
        $this->actionsChain->execute('insert', $node);

        $this->storage->beginTransaction();

        try {
            $data = $this->extract($node);

            $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                'node',
                $node->getId()->getId(),
                $data['attributes']
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($node->collectDomainEvents());
    }

    public function update(Node $node): void
    {
        $this->actionsChain->execute('update', $node);

        $this->storage->beginTransaction();

        try {
            $data = $this->extract($node);

            $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                'node',
                $node->getId()->getId(),
                $data['attributes']
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection(array_merge($node->collectDomainEvents(), [NodeUpdated::fromNode($node)]));
    }

    public function delete(Node $node): void
    {
        $this->actionsChain->execute('delete', $node);

        $this->storage->beginTransaction();

        try {
            $this->storage->delete($this->extract($node));
            $this->metadataRepository->delete('node', $node->getId()->getId());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(NodeDeleted::fromNode($node));
    }

    private function extract(Node $node): array
    {
        $attributes = [];

        foreach ($node->getAttributes() as $uri => $attribute) {
            if (\in_array($uri, self::RESERVED_NAMES)) {
                continue;
            }

            $attributes[$uri] = $attribute;
        }

        $result = [
            'id'            => $node->getId()->getId(),
            'type'          => $node->getType(),
            'website_id'    => $node->getWebsiteId(),
            'published_at'  => $node->getPublishedAt()->format('Y-m-d H:i:s'),
            'published_to'  => $node->getPublishedTo() ? $node->getPublishedTo()->format('Y-m-d H:i:s') : null,
            'created_at'    => $node->getCreatedAt(),
            'updated_at'    => $node->getUpdatedAt(),
            'status'        => $node->getStatus(),
            'author_id'     => $node->getAuthorId(),
            'category_id'   => $node->getCategoryId(),
            'level'         => $node->getLevel(),
            'parent_id'     => $node->getParentId(),
            'locale'        => $node->getLocale(),
            'title'         => $node->getTitle(),
            'slug'          => $node->getSlug(),
            'attributes'    => $attributes,
        ];

        return $result;
    }

    /**
     * @param Field[] $fields
     */
    private function buildAttributesMapping(array $fields, string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                foreach ($this->buildAttributesMapping($field->getChildren(), $prefix.$field->getCode().'.') as $code => $subfield) {
                    $result[$code] = $subfield;
                }
            } else {
                $result[$prefix.$field->getCode()] = [
                    'is_multilingual' => $field->isMultilingual(),
                    'is_compilable' => $field->hasFlag('compilable'),
                    'has_nonscalar_value' => $field->hasNonscalarValue(),
                ];

                if ($field->hasFlag('compilable')) {
                    $result[$prefix.$field->getCode().':compiled'] = [
                        'is_multilingual' => $field->isMultilingual(),
                        'is_compilable' => false,
                        'has_nonscalar_value' => $field->hasNonscalarValue(),
                    ];
                }
            }
        }

        return $result;
    }
}
