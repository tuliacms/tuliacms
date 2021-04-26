<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Query;

use PDO;
use Exception;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\AbstractQuery;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractQuery
{
    protected QueryBuilder $queryBuilder;

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
        $this->searchBySlug($query);
        $this->searchByName($query);
        $this->setDefaults($query);
        $this->buildTaxonomyType($query);
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
            $this->queryBuilder->select('tm.*, tl.*');
        }

        $this->queryBuilder
            ->from('#__term', 'tm')
            ->innerJoin('tm', '#__term_lang', 'tl', 'tm.id = tl.term_id')
            ->andWhere('tl.locale = :tl_locale')
            ->setParameter('tl_locale', $query['locale'], PDO::PARAM_STR);
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

        if ($query['children_of']) {
            if (\is_array($query['children_of']) === false) {
                $ids = [ $query['children_of'] ];
            } else {
                $ids = $query['children_of'];
            }

            $this->queryBuilder
                ->andWhere('tm.parent_id IN (:tm_children_of)')
                ->setParameter('tm_children_of', $ids, Connection::PARAM_STR_ARRAY);
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
    protected function searchBySlug(array $query): void
    {
        if (! $query['slug']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.slug = :tl_slug')
            ->setParameter('tl_slug', $query['slug'], PDO::PARAM_STR)
            ->setMaxResults(1);
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
            ->andWhere('tl.name LIKE :tl_name')
            ->setParameter('tl_name', '%' . $query['search'] . '%', PDO::PARAM_STR);
    }

    /**
     * @param array $query
     */
    protected function buildTaxonomyType(array $query): void
    {
        $types    = \is_array($query['taxonomy_type'])      ? $query['taxonomy_type']      : [ $query['taxonomy_type'] ];
        $typesNot = \is_array($query['taxonomy_type__not']) ? $query['taxonomy_type__not'] : [ $query['taxonomy_type__not'] ];

        if ($query['taxonomy_type'] !== null && $query['taxonomy_type'] !== 'any' && $types !== []) {
            $this->queryBuilder
                ->andWhere('tm.type IN (:tm_type_in)')
                ->setParameter('tm_type_in', $types, Connection::PARAM_STR_ARRAY);
        }

        if ($query['taxonomy_type__not'] !== null && $typesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.type NOT IN (:tm_type_not_in)')
                ->setParameter('tm_type_not_in', $typesNot, Connection::PARAM_STR_ARRAY);
        }
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
        if ($query['order_hierarchical']) {
            $this->queryBuilder->addOrderBy('tm.level', 'ASC');
        }

        if ($query['order_by']) {
            $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
        }
    }
}
