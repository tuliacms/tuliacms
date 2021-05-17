<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\Metadata\Domain\WriteModel\MetadataRepository;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\NodeMetadataEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\WriteModel\NodeWriteStorageInterface;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeRepository
{
    private NodeWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private MetadataRepository $metadataRepository;

    private HydratorInterface $hydrator;

    public function __construct(
        NodeWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        MetadataRepository $metadataRepository,
        HydratorInterface $hydrator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->metadataRepository = $metadataRepository;
        $this->hydrator = $hydrator;
    }

    /**
     * @throws NodeNotFoundException
     * @throws \Exception
     */
    public function find(string $id): Node
    {
        $node = $this->storage->find($id, $this->currentWebsite->getLocale()->getCode());

        if (empty($node)) {
            throw new NodeNotFoundException();
        }

        /** @var Node $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'           => new AggregateId($node['id']),
            'type'         => $node['type'] ?? '',
            'websiteId'    => $node['website_id'],
            'publishedAt'  => new ImmutableDateTime($node['published_at']),
            'publishedTo'  => $node['published_to'] ? new ImmutableDateTime($node['published_to']) : null,
            'createdAt'    => new ImmutableDateTime($node['created_at']),
            'updatedAt'    => $node['updated_at'] ? new ImmutableDateTime($node['updated_at']) : null,
            'status'       => $node['status'] ?? '',
            'authorId'     => $node['author_id'] ?? '',
            'category'     => $node['category'] ?? null,
            'slug'         => $node['slug'] ?? '',
            'title'        => $node['title'] ?? '',
            'content'      => $node['content'] ?? '',
            'contentSource'=> $node['content_source'] ?? '',
            'introduction' => $node['introduction'] ?? '',
            'level'        => (int) $node['level'],
            'parentId'     => $node['parent_id'] ?? '',
            'locale'       => $node['locale'],
            'metadata'     => $this->metadataRepository->findAll(NodeMetadataEnum::TYPE, $id),
            'translated'   => $node['translated'] ?? true,
        ], Node::class);

        return $aggregate;
    }

    public function create(Node $node): void
    {

    }

    public function update(Node $node): void
    {

    }

    public function delete(Node $node): void
    {

    }

}
