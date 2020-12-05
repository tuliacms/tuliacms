<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy;

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
class DbalTermStorage implements TermStorageInterface
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
    public function find(string $termId, string $locale): ?array
    {
        $result = $this->connection->fetchAll('
            SELECT slug, locale, visibility, level, parent_id
            FROM #__term AS tm
            INNER JOIN #__term_lang AS tl
                ON tm.id = tl.term_id
            WHERE term_id = :term_id AND locale = :locale
        ', [
            'term_id' => $termId,
            'locale'  => $locale,
        ]);

        return $result[0] ?? null;
    }
}
