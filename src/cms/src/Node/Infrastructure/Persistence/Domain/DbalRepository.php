<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Domain;

use Tulia\Cms\Node\Domain\Enum\TermTypeEnum;
use Tulia\Cms\Node\Domain\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\ValueObject\AggregateId;
use Tulia\Cms\Node\Domain\Aggregate\Node;
use Tulia\Cms\Node\Domain\RepositoryInterface;
use Tulia\Cms\Metadata\Metadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var DbalPersister
     */
    protected $persister;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var SyncerInterface
     */
    protected $metadata;

    /**
     * @param ConnectionInterface $connection
     * @param DbalPersister $persister
     * @param HydratorInterface $hydrator
     * @param SyncerInterface $metadata
     */
    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        HydratorInterface $hydrator,
        SyncerInterface $metadata
    ) {
        $this->connection = $connection;
        $this->persister  = $persister;
        $this->hydrator   = $hydrator;
        $this->metadata   = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id, string $locale): Node
    {
        $node = $this->connection->fetchAll('
            SELECT *
            FROM #__node AS tm
            INNER JOIN #__node_lang AS tl
                ON tm.id = tl.node_id
            WHERE tm.id = :id AND tl.locale = :locale
            LIMIT 1', [
            'id'     => $id->getId(),
            'locale' => $locale
        ]);

        if (empty($node)) {
            throw new NodeNotFoundException();
        }

        $terms = $this->connection->fetchAll('
            SELECT *
            FROM #__node_term_relationship
            WHERE node_id = :node_id', [
            'node_id' => $id->getId()
        ]);

        $category = null;

        foreach ($terms as $term) {
            if ($term['type'] === TermTypeEnum::MAIN) {
                $category = $term['term_id'];
            }
        }

        $node = reset($node);

        /** @var Node $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'           => new AggregateId($node['id']),
            'type'         => $node['type'] ?? '',
            'websiteId'    => $node['website_id'],
            'publishedAt'  => new ImmutableDateTime($node['published_at']),
            'publishedTo'  => $node['published_to'] ? new ImmutableDateTime($node['published_to']) : null,
            'status'       => $node['status'] ?? '',
            'authorId'     => $node['author_id'] ?? '',
            'category'     => $category,
            'slug'         => $node['slug'] ?? '',
            'title'        => $node['title'] ?? '',
            'content'      => $node['content_source'] ?? '',
            'introduction' => $node['introduction'] ?? '',
            'level'        => (int) $node['level'],
            'parentId'     => $node['parent_id'] ?? '',
            'locale'       => $node['locale'],
            'metadata'     => $this->metadata->all('node', $id->getId()),
        ], Node::class);

        return $aggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Node $node): void
    {
        $data = $this->extract($node);

        $this->connection->transactional(function () use ($data) {
            if ($this->recordExists($data['id'])) {
                $this->persister->update($data);
            } else {
                $this->persister->insert($data);
            }

            $this->metadata->push(
                new Metadata($data['metadata']),
                'node',
                $data['id']
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Node $node): void
    {
        $data = $this->extract($node);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);

            $this->metadata->delete(
                'node',
                $data['id']
            );
        });
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAll('SELECT id FROM #__node WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    private function extract(Node $node): array
    {
        $data = $this->hydrator->extract($node);
        $data['id'] = $node->getId()->getId();

        return $data;
    }
}
