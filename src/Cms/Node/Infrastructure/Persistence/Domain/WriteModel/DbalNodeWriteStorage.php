<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\Enum\TermTypeEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\WriteModel\NodeWriteStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeWriteStorage implements NodeWriteStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $id, string $locale): array
    {
        $node = $this->connection->fetchAll('
            SELECT *, "1" AS translated
            FROM #__node AS tm
            INNER JOIN #__node_lang AS tl
                ON tm.id = tl.node_id
            WHERE tm.id = :id AND tl.locale = :locale
            LIMIT 1', [
            'id'     => $id,
            'locale' => $locale
        ]);

        if ($node === []) {
            return [];
        }

        $terms = $this->connection->fetchAll('
            SELECT *
            FROM #__node_term_relationship
            WHERE node_id = :node_id', [
            'node_id' => $id
        ]);

        $node[0]['category'] = null;

        foreach ($terms as $term) {
            if ($term['type'] === TermTypeEnum::MAIN) {
                $node[0]['category'] = $term['term_id'];
            }
        }

        return $node[0];
    }

    public function create(array $node): void
    {
        // TODO: Implement create() method.
    }

    public function update(array $node): void
    {
        // TODO: Implement update() method.
    }

    public function delete(array $node): void
    {
        // TODO: Implement delete() method.
    }
}
