<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Query;

use PDO;
use Exception;
use Tulia\Cms\Widget\Query\Exception\QueryException;
use Tulia\Cms\Widget\Query\AbstractQuery;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Database\ConnectionInterface;
use Tulia\Framework\Database\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractQuery
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
     * {@inheritdoc}
     */
    public function execute(array $query): array
    {
        $this->searchById($query);
        $this->searchByName($query);
        $this->setDefaults($query);
        $this->buildSpace($query);
        $this->buildVisibility($query);
        $this->buildOffset($query);
        $this->buildOrderBy($query);

        if ($query['website']) {
            $this->queryBuilder
                ->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $query['website'], PDO::PARAM_STR);
        }

        return $this->queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * {@inheritdoc}
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
            $this->queryBuilder->select('
                tm.*,
                tl.locale,
                IF(ISNULL(tl.title), 0, 1) AS translated,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized) AS payload_localized,
                tm.widget_id AS widget_id
            ');
        }

        $this->queryBuilder
            ->from('#__widget', 'tm')
            ->leftJoin('tm', '#__widget_lang', 'tl', 'tm.id = tl.widget_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $query['locale'], PDO::PARAM_STR);
    }

    /**
     * @param array $query
     */
    protected function searchByName(array $query): void
    {
        if (! $query['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.name LIKE :tm_name OR tl.title = :tm_name')
            ->setParameter('tm_name', '%' . $query['search'] . '%', PDO::PARAM_STR);
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
    protected function buildSpace(array $query): void
    {
        if (! $query['space']) {
            return;
        }

        if (\is_array($query['space']) === false) {
            $query['space'] = [$query['space']];
        }

        $this->queryBuilder
            ->andWhere('tm.space IN(:tm_spaces)')
            ->setParameter('tm_spaces', $query['space'], ConnectionInterface::PARAM_ARRAY_STR);
    }

    /**
     * @param array $query
     */
    protected function buildVisibility(array $query): void
    {
        if (! $query['visibility']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.visibility = :tl_visibility')
            ->setParameter('tl_visibility', $query['visibility'], PDO::PARAM_INT);
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
            $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
        }
    }
}
