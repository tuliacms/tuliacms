<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermPathReadStorage implements TermPathReadStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $termId, string $locale): array
    {
        $result = $this->connection->fetchAll('
            SELECT *
            FROM #__term_path
            WHERE term_id = :term_id AND locale = :locale
            LIMIT 1
        ', [
            'term_id' => $termId,
            'locale'  => $locale,
        ]);

        return $result[0] ?? [];
    }
}
