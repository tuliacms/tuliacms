<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermStorage implements TermStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $termId, string $locale): ?array
    {
        $result = $this->connection->fetchAll('
            SELECT
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.locale, :locale) AS locale
            FROM #__term AS tm
            LEFT JOIN #__term_lang AS tl
                ON tm.id = tl.term_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1
        ', [
            'id' => $termId,
            'locale' => $locale,
        ]);

        return $result[0] ?? null;
    }
}
