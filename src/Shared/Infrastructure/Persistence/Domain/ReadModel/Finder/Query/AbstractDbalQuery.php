<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Exception;
use Doctrine\DBAL\Driver\Exception as DoctrineException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDbalQuery extends AbstractQuery
{
    public const STORAGE_NAME = 'doctrine.query-builder';

    protected QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getSupportedStorage(): string
    {
        return self::STORAGE_NAME;
    }

    /**
     * @return int
     * @throws QueryException
     */
    public function countFoundRows(): int
    {
        try {
            $result = (clone $this->queryBuilder)
                ->select('COUNT(id) AS count')
                ->setMaxResults(null)
                ->setFirstResult(null)
                ->execute()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new QueryException('Exception during countFoundRows() call: ' . $e->getMessage(), 0, $e);
        } catch (DoctrineException $e) {
            throw new QueryException('Exception during countFoundRows() call: ' . $e->getMessage(), 0, $e);
        }

        return (int) ($result[0]['count'] ?? 0);
    }
}
