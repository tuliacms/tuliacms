<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain;

use Tulia\Cms\Taxonomy\Domain\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;
use Tulia\Cms\Taxonomy\Domain\Aggregate\Term;
use Tulia\Cms\Taxonomy\Domain\RepositoryInterface;
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
    public function find(AggregateId $id, string $locale): Term
    {
        $term = $this->connection->fetchAll('
            SELECT *
            FROM #__term AS tm
            INNER JOIN #__term_lang AS tl
                ON tm.id = tl.term_id
            WHERE tm.id = :id AND tl.locale = :locale
            LIMIT 1', [
            'id'     => $id->getId(),
            'locale' => $locale
        ]);

        if (empty($term)) {
            throw new TermNotFoundException();
        }

        $term = reset($term);

        /** @var Term $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'         => new AggregateId($term['id']),
            'type'       => $term['type'] ?? '',
            'websiteId'  => $term['website_id'],
            'slug'       => $term['slug'] ?? '',
            'path'       => $term['path'] ?? '',
            'name'       => $term['name'] ?? '',
            'parentId'   => $term['parent_id'] ?? '',
            'locale'     => $term['locale'],
            'visibility' => (bool) $term['visibility'],
            'metadata'   => $this->metadata->all('term', $id->getId()),
        ], Term::class);

        return $aggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Term $term): void
    {
        $data = $this->extract($term);

        $this->connection->transactional(function () use ($data) {
            if ($this->recordExists($data['id'])) {
                $this->persister->update($data);
            } else {
                $this->persister->insert($data);
            }

            $this->metadata->push(
                new Metadata($data['metadata']),
                'term',
                $data['id']
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Term $term): void
    {
        $data = $this->extract($term);

        $this->connection->transactional(function () use ($data) {
            $this->persister->delete($data);

            $this->metadata->delete(
                'term',
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
        $result = $this->connection->fetchAll('SELECT id FROM #__term WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    private function extract(Term $term): array
    {
        $data = $this->hydrator->extract($term);
        $data['id'] = $term->getId()->getId();

        return $data;
    }
}
