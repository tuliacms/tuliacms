<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

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

    public function findTermToPathGeneration(string $termId, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('
            SELECT
                t.type,
                t.parent_id,
                COALESCE(tl.slug, t.slug) AS slug
            FROM #__term AS t
            LEFT JOIN #__term_lang AS tl
                ON tl.locale = :locale
            WHERE t.id = :term_id
            LIMIT 1
        ', [
            'term_id' => $termId,
            'locale'  => $locale,
        ]);

        return $result[0] ?? [];
    }

    public function findPathByTermId(string $termId, string $locale): array
    {
        $result = $this->connection->fetchAllAssociative('
            SELECT tp.*
            FROM #__term_path AS tp
            WHERE tp.term_id = :term_id AND tp.locale = :locale
            LIMIT 1
        ', [
            'term_id' => $termId,
            'locale'  => $locale,
        ]);

        return $result[0] ?? [];
    }

    public function findTermIdByPath(string $path, string $locale): ?string
    {
        $result = $this->connection->fetchAllAssociative('
            SELECT term_id
            FROM #__term_path
            WHERE path = :path AND locale = :locale
            LIMIT 1
        ', [
            'path' => $path,
            'locale'  => $locale,
        ]);

        return $result[0]['term_id'] ?? null;
    }
}
