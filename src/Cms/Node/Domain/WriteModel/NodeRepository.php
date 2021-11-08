<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\NodeTypeNotExistsException;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionsChainInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\ValueObject\AttributeInfo;
use Tulia\Cms\Node\Domain\WriteModel\Ports\NodeWriteStorageInterface;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeRepository
{
    private NodeWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private MetadataRepository $metadataRepository;

    private UuidGeneratorInterface $uuidGenerator;

    private EventBusInterface $eventBus;

    private NodeActionsChainInterface $actionsChain;

    private NodeTypeRegistry $nodeTypeRegistry;

    public function __construct(
        NodeWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        NodeActionsChainInterface $actionsChain,
        NodeTypeRegistry $nodeTypeRegistry
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
        $this->nodeTypeRegistry = $nodeTypeRegistry;
    }

    public function createNew(string $nodeType): Node
    {
        $type = $this->nodeTypeRegistry->get($nodeType);

        $node = Node::createNew(
            $this->uuidGenerator->generate(),
            $nodeType,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );

        foreach ($this->buildAttributesMapping($type) as $name => $info) {
            $node->addAttributeInfo($name, new AttributeInfo(
                $info['multilingual'],
                $info['multiple'],
                $info['compilable'],
            ));
        }

        return $node;
    }

    /**
     * @throws NodeNotFoundException
     * @throws NodeTypeNotExistsException
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

        $nodeType = $this->nodeTypeRegistry->get($node['type']);

        $attributes = $this->metadataRepository->findAll('node', $id);

        foreach ($nodeType->getFields() as $field) {
            if ($field->isMultiple()) {
                $attributes[$field->getName()] = (array) unserialize((string) $attributes[$field->getName()], ['allowed_classes' => []]);
            }
        }

        $node = Node::buildFromArray($node['type'], [
            'id'            => $node['id'],
            'website_id'    => $node['website_id'],
            'published_at'  => new ImmutableDateTime($node['published_at']),
            'published_to'  => $node['published_to'] ? new ImmutableDateTime($node['published_to']) : null,
            'created_at'    => new ImmutableDateTime($node['created_at']),
            'updated_at'    => $node['updated_at'] ? new ImmutableDateTime($node['updated_at']) : null,
            'status'        => $node['status'] ?? '',
            'author_id'     => $node['author_id'] ?? null,
            'level'         => (int) $node['level'],
            'parent_id'     => $node['parent_id'] ?? null,
            'locale'        => $node['locale'],
            'translated'    => $node['translated'] ?? true,
            'attributes'    => $attributes,
            'attributes_mapping' => $this->buildAttributesMapping($nodeType),
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

        foreach ($node->getAttributes() as $name => $value) {
            $info = $node->getAttributeInfo($name);

            $attributes[$name] = [
                'value' => $value,
                'multilingual' => $info->isMultilingual(),
                'multiple' => $info->isMultiple(),
            ];
        }

        return [
            'id'            => $node->getId()->getId(),
            'type'          => $node->getType(),
            'website_id'    => $node->getWebsiteId(),
            'published_at'  => $node->getPublishedAt(),
            'published_to'  => $node->getPublishedTo(),
            'created_at'    => $node->getCreatedAt(),
            'updated_at'    => $node->getUpdatedAt(),
            'status'        => $node->getStatus(),
            'author_id'     => $node->getAuthorId(),
            'category_id'   => $node->getCategoryId(),
            'level'         => $node->getLevel(),
            'parent_id'     => $node->getParentId(),
            'locale'        => $node->getLocale(),
            'attributes'    => $attributes,
        ];
    }

    private function buildAttributesMapping(NodeType $nodeType): array
    {
        $result = [];

        foreach ($nodeType->getFields() as $field) {
            $result[$field->getName()] = [
                'multilingual' => $field->isMultilingual(),
                'multiple' => $field->isMultiple(),
                'compilable' => $field->hasFlag('compilable'),
            ];
        }

        return $result;
    }
}
