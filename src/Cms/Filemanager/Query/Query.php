<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Query;

use PDO;
use Exception;
use Tulia\Cms\Filemanager\Collection;
use Tulia\Cms\Filemanager\CollectionInterface;
use Tulia\Cms\Filemanager\File;
use Tulia\Cms\Filemanager\Exception\QueryException;
use Tulia\Framework\Database\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class Query
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return array
     */
    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for node with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'id' => null,
            'id__in' => null,
            /**
             * Search for nodes that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * Type of file. `null` means searching for every type.
             *
             * @param null|string|array
             */
            'type' => null,
            /**
             * @param null|int
             */
            'per_page' => null,
            /**
             * @param null|int
             */
            'page' => null,
            'order_by' => 'created_at',
            'order_dir' => 'DESC',
            /**
             * Directory where files should be placed.
             *
             * @param null|string|array
             */
            'directory' => null,
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Locale of the node to fetch.
             */
            'locale' => 'en_US',
        ];
    }

    /**
     * @param array $query
     *
     * @return CollectionInterface
     *
     * @throws QueryException
     */
    public function query(array $query): CollectionInterface
    {
        $base = $this->getBaseQueryArray();

        return $this->createCollection($this->execute(array_merge($base, $query)));
    }

    /**
     * @param array $query
     *
     * @return CollectionInterface
     *
     * @throws QueryException
     */
    public function queryRaw(array $query): CollectionInterface
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
        $this->searchById($query);
        $this->setDefaults($query);
        $this->buildType($query);
        $this->buildDirectory($query);
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
     * @return CollectionInterface
     *
     * @throws QueryException
     */
    protected function createCollection(array $result): CollectionInterface
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        try {
            foreach ($result as $row) {
                $collection->append(File::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: '.$e->getMessage(), 0, $e);
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

        $this->queryBuilder->from('#__filemanager_file', 'tm');
    }

    /**
     * @param array $query
     */
    protected function buildType(array $query): void
    {
        if ($query['type']) {
            if (\is_array($query['type'])) {
                $this->queryBuilder
                    ->andWhere('tm.type IN (:tm_type)')
                    ->setParameter('tm_type', $query['type'], Connection::PARAM_STR_ARRAY);
            } else {
                $this->queryBuilder
                    ->andWhere('tm.type = :tm_type')
                    ->setParameter('tm_type', $query['type']);
            }
        }
    }

    /**
     * @param array $query
     */
    protected function buildDirectory(array $query): void
    {
        if ($query['directory']) {
            if (\is_array($query['directory'])) {
                $this->queryBuilder
                    ->andWhere('tm.directory IN (:tm_directory)')
                    ->setParameter('tm_directory', $query['directory'], Connection::PARAM_STR_ARRAY);
            } else {
                $this->queryBuilder
                    ->andWhere('tm.directory = :tm_directory')
                    ->setParameter('tm_directory', $query['directory']);
            }
        }
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

        if ($query['id__in']) {
            if (\is_array($query['id__in']) === false) {
                $ids = [ $query['id__in'] ];
            } else {
                $ids = $query['id__in'];
            }

            $this->queryBuilder
                ->andWhere('tm.id IN (:tm_id__in)')
                ->setParameter('tm_id__in', $ids, Connection::PARAM_STR_ARRAY);
        }
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
            if (\is_array($query['order_dir'])) {
                $field = $query['order_by'];
                $ids = array_map(function ($id) {
                    return "'{$id}'";
                }, $query['order_dir']);

                $this->queryBuilder->addOrderBy(sprintf('FIELD(tm.`%s`, %s)', $field, implode(', ', $ids)));
            } else {
                $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
            }
        }
    }
}
