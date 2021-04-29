<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\ReadModel;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\AbstractDbalQuery;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Domain\ReadModel\Model\Locale;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    protected QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

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
            /**
             * Search for nodes that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * @param null|int|bool
             */
            'active' => null,
            'order_by' => 'name',
            'order_dir' => 'ASC',
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
        ];
    }

    public function query(array $query, array $parameters = []): Collection
    {
        $query = array_merge($this->getBaseQueryArray(), $query);

        $this->searchById($query);
        $this->setDefaults($query);
        $this->buildActivity($query);
        $this->buildOrderBy($query);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative());
    }

    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        try {
            foreach ($result as $row) {
                $collection->append(new Website($row['id'], [], $row['backend_prefix'], $row['name']));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        $this->fillWebsitesWithLocales($collection);

        return $collection;
    }

    protected function getCountFromResult(array $result): int
    {
        return (int) ($result[0]['count'] ?? 0);
    }

    protected function setDefaults(array $query): void
    {
        if ($query['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($query['count']) {
            $this->queryBuilder->select('COUNT(' . $query['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('tm.*');
        }

        $this->queryBuilder->from('#__website', 'tm');
    }

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

    protected function buildActivity(array $query): void
    {
        if (! $query['active']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.active = :tm_active')
            ->setParameter('tm_active', $query['active'], PDO::PARAM_INT);
    }

    /**
     * @param array $query
     */
    protected function buildOrderBy(array $query): void
    {
        if ($query['order_by']) {
            $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
        }
    }

    protected function fillWebsitesWithLocales(Collection $collection): void
    {
        $locales = $this->queryBuilder->getConnection()->fetchAllAssociative(
            'SELECT * FROM #__website_locale whl
            WHERE whl.website_id IN (:websiteIdList)
            ORDER BY `is_default` DESC', [
            'websiteIdList' => array_map(function ($website) {
                return $website->getId();
            }, $collection->all())
        ], [
            'websiteIdList' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);

        foreach ($locales as $locale) {
            /** @var Website $website */
            foreach ($collection as $website) {
                if ($website->getId() === $locale['website_id']) {
                    $localeObject = new Locale(
                        $locale['code'],
                        $locale['domain'],
                        $locale['locale_prefix'],
                        $locale['path_prefix'],
                        $locale['ssl_mode'],
                        (bool) $locale['is_default']
                    );
                    $website->addLocale($localeObject);

                    /*if ($locale['is_default']) {
                        $website->setDefaultLocale($localeObject);
                    }*/
                }
            }
        }
    }
}
