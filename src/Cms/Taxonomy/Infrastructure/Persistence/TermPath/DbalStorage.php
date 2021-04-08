<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath;

use PDO;
use Exception;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\AbstractQuery;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Database\ConnectionInterface;
use Tulia\Framework\Database\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalStorage implements StorageInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
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

    /**
     * {@inheritdoc}
     */
    public function remove(string $termId, string $locale): void
    {
        $this->connection->delete('#__term_path', [
            'term_id' => $termId,
            'locale'  => $locale,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $termId, string $locale, string $path): void
    {
        $existent = $this->find($termId, $locale);

        if (empty($existent)) {
            $this->connection->insert('#__term_path', [
                'term_id' => $termId,
                'locale'  => $locale,
                'path'    => $path,
            ]);
        } elseif ($existent['path'] !== $path) {
            $this->connection->update('#__term_path', [
                'path'    => $path,
            ], [
                'term_id' => $termId,
                'locale'  => $locale,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findByPath(string $path, string $locale): ?string
    {
        $result = $this->connection->fetchAll('
            SELECT *
            FROM #__term_path
            WHERE `path` = :path AND locale = :locale
            LIMIT 1
        ', [
            'path'   => $path,
            'locale' => $locale,
        ]);

        return $result[0]['term_id'] ?? null;
    }
}
