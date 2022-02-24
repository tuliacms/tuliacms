<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use PDO;
use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;
use Tulia\Cms\User\Domain\ReadModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinderQuery extends AbstractDbalQuery
{
    protected QueryBuilder $queryBuilder;
    protected AttributesFinder $attributesFinder;

    public function __construct(
        QueryBuilder $queryBuilder,
        AttributesFinder $attributesFinder
    ) {
        parent::__construct($queryBuilder);

        $this->queryBuilder = $queryBuilder;
        $this->attributesFinder = $attributesFinder;
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
            'order_by' => 'email',
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
    public function query(array $query, string $scope): Collection
    {
        $base = $this->getBaseQueryArray();

        return $this->createCollection($this->execute(array_merge($base, $query)), $scope);
    }

    /**
     * @param array $query
     *
     * @return Collection
     */
    public function queryRaw(array $query, string $scope): Collection
    {
        return $this->createCollection($this->execute(array_merge($this->getBaseQueryArray(), $query)), $scope);
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
        $this->searchByEmail($query);
        $this->search($query);
        $this->buildOffset($query);
        $this->buildOrderBy($query);

        return $this->queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * @param array $result
     *
     * @return Collection
     */
    protected function createCollection(array $result, string $scope): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $metadata = $this->attributesFinder->findAllAggregated('user', $scope, array_column($result, 'id'));

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
            ->andWhere('tm.email LIKE :tm_search')
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
