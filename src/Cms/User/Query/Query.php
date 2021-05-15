<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query;

use Exception;
use PDO;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\UserMetadataEnum;
use Tulia\Cms\User\Query\Model\Collection;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\User\Query\Exception\QueryException;

/**
 * @author Adam Banaszkiewicz
 */
class Query
{
    protected QueryBuilder $queryBuilder;
    protected MetadataFinder $metadataFinder;

    public function __construct(QueryBuilder $queryBuilder, MetadataFinder $metadataFinder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->metadataFinder = $metadataFinder;
    }

    /**
     * @return array
     */
    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for user with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'id' => null,
            /**
             * Search for users that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * Search for user with given username.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'username' => null,
            /**
             * Search for user with given email address.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'email' => null,
            /**
             * @param null|int
             */
            'per_page' => null,
            /**
             * @param null|int
             */
            'page' => null,
            /**
             * This field has higher priority than order_by and order_dir.
             * Allows to define custom sort option.
             */
            /*'order' => null,*/
            'order_by' => 'username',
            'order_dir' => 'DESC',
            /**
             * Search string. Seaching by title with LIKE operator.
             */
            'search' => null,
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
        ];
    }

    /**
     * @param array $query
     *
     * @return Collection
     */
    public function query(array $query): Collection
    {
        $base = $this->getBaseQueryArray();

        return $this->createCollection($this->execute(array_merge($base, $query)));
    }

    /**
     * @param array $query
     *
     * @return Collection
     */
    public function queryRaw(array $query): Collection
    {
        return $this->createCollection($this->execute(array_merge($this->getBaseQueryArray(), $query)));
    }

    /**
     * @param array $query
     *
     * @return int
     */
    public function count(array $query): int
    {
        $base = $this->getBaseQueryArray();
        $base['count'] = true;

        return $this->getCountFromResult($this->execute(array_merge($base, $query)));
    }

    /**
     * @param array $query
     *
     * @return int
     */
    public function countRaw(array $query): int
    {
        $base = $this->getBaseQueryArray();
        $base['count'] = true;

        return $this->getCountFromResult($this->execute(array_merge($base, $query)));
    }

    /**
     * @param array $query
     *
     * @return array
     */
    public function execute(array $query): array
    {
        $this->setDefaults($query);
        $this->searchById($query);
        $this->searchByUsername($query);
        $this->searchByEmail($query);
        $this->search($query);
        $this->buildOffset($query);
        $this->buildOrderBy($query);

        return $this->queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * @return int
     *
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
        }

        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * @param array $result
     *
     * @return Collection
     */
    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $metadata = $this->metadataFinder->findAllAggregated(UserMetadataEnum::TYPE, array_column($result, 'id'));

        foreach ($result as $row) {
            $row['metadata'] = $metadata[$row['id']] ?? [];

            $collection->append(User::buildFromArray($row));
        }

        return $collection;
    }

    /**
     * @param array $result
     *
     * @return int
     */
    protected function getCountFromResult(array $result): int
    {
        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * @param array $query
     */
    protected function setDefaults(array $query): void
    {
        if ($query['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($query['count']) {
            $this->queryBuilder->select('COUNT(' . $query['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('tm.*');
        }

        $this->queryBuilder->from('#__user', 'tm');
    }

    /**
     * @param array $query
     */
    protected function searchById(array $query): void
    {
        if ($query['id']) {
            $this->queryBuilder
                ->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', $query['id'], PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($query['id__not_in']) {
            if (\is_array($query['id__not_in']) === false) {
                $ids = [ $query['id__not_in'] ];
            } else {
                $ids = $query['id__not_in'];
            }

            $this->queryBuilder
                ->andWhere('tm.id NOT IN (:tm_id__not_in)')
                ->setParameter('tm_id__not_in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    /**
     * @param array $query
     */
    protected function searchByUsername(array $query): void
    {
        if (! $query['username']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.username = :tm_username')
            ->setParameter('tm_username', $query['username'], PDO::PARAM_STR)
            ->setMaxResults(1);
    }

    /**
     * @param array $query
     */
    protected function searchByEmail(array $query): void
    {
        if (! $query['email']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.email = :tm_email')
            ->setParameter('tm_email', $query['email'], PDO::PARAM_STR)
            ->setMaxResults(1);
    }

    /**
     * @param array $query
     */
    protected function search(array $query): void
    {
        if (! $query['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.username LIKE :tm_search')
            ->setParameter('tm_search', '%' . $query['search'] . '%', PDO::PARAM_STR);
    }

    /**
     * @param array $query
     */
    protected function buildOffset(array $query): void
    {
        if ($query['per_page'] && $query['page']) {
            $this->queryBuilder->setFirstResult($query['page'] <= 1 ? 0 : ($query['per_page'] * ($query['page'] - 1)));
        }

        if ($query['per_page']) {
            $this->queryBuilder->setMaxResults($query['per_page']);
        }
    }

    /**
     * @param array $query
     */
    protected function buildOrderBy(array $query): void
    {
        if ($query['order_by']) {
            $this->queryBuilder->orderBy($query['order_by'], $query['order_dir']);
        }
    }
}
