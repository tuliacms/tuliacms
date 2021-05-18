<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\ActionsChainInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\NodeMetadataEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\WriteModel\NodeWriteStorageInterface;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;
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

    private ActionsChainInterface $actionsChain;

    public function __construct(
        NodeWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        ActionsChainInterface $actionsChain
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
    }

    public function createNew(array $data): Node
    {
        return Node::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'locale' => $this->currentWebsite->getLocale()->getCode(),
            'node_type' => 'page',
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }

    /**
     * @throws NodeNotFoundException
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

        $node = Node::buildFromArray([
            'id'            => $node['id'],
            'type'          => $node['type'] ?? '',
            'website_id'    => $node['website_id'],
            'published_at'  => new ImmutableDateTime($node['published_at']),
            'published_to'  => $node['published_to'] ? new ImmutableDateTime($node['published_to']) : null,
            'created_at'    => new ImmutableDateTime($node['created_at']),
            'updated_at'    => $node['updated_at'] ? new ImmutableDateTime($node['updated_at']) : null,
            'status'        => $node['status'] ?? '',
            'author_id'     => $node['author_id'] ?? '',
            'category'      => $node['category'] ?? null,
            'slug'          => $node['slug'] ?? '',
            'title'         => $node['title'] ?? '',
            'content'       => $node['content'] ?? '',
            'content_source' => $node['content_source'] ?? '',
            'introduction'  => $node['introduction'] ?? '',
            'level'         => (int) $node['level'],
            'parent_id'     => $node['parent_id'] ?? '',
            'locale'        => $node['locale'],
            'metadata'      => $this->metadataRepository->findAll(NodeMetadataEnum::TYPE, $id),
            'translated'    => $node['translated'] ?? true,
        ]);

        $this->actionsChain->execute('find', $node);

        return $node;
    }

    public function insert(Node $node): void
    {
        $this->actionsChain->execute('insert', $node);

        $this->storage->beginTransaction();

        try {
            $this->storage->insert($this->extract($node), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                NodeMetadataEnum::TYPE,
                $node->getId()->getId(),
                $node->getAllMetadata()
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
            $this->storage->update($this->extract($node), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->metadataRepository->persist(
                NodeMetadataEnum::TYPE,
                $node->getId()->getId(),
                $node->getAllMetadata()
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
            $this->metadataRepository->delete(NodeMetadataEnum::TYPE, $node->getId()->getId());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(NodeDeleted::fromNode($node));
    }

    private function extract(Node $node): array
    {
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
            'category'      => $node->getCategory(),
            'slug'          => $node->getSlug(),
            'title'         => $node->getTitle(),
            'content'       => $node->getContent(),
            'content_compiled' => $node->getContentCompiled(),
            'introduction'  => $node->getIntroduction(),
            'level'         => $node->getLevel(),
            'parent_id'     => $node->getParentId(),
            'locale'        => $node->getLocale(),
        ];
    }
}
