<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Query;

use PDO;
use Exception;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\AbstractQuery;
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
     * @var array
     */
    private $joinedTables = [];

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
        $this->searchBySlug($query);
        $this->searchByTitle($query);
        $this->setDefaults($query);
        $this->buildCategory($query);
        $this->buildTaxonomy($query);
        $this->buildNodeType($query);
        $this->buildNodeStatus($query);
        $this->buildDate($query);
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
     * {@inheritdoc}
     */
    protected function getCountFromResult(array $result): int
    {
        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchTerms(array $nodeIdList): array
    {
        $source = $this->queryBuilder->getConnection()->fetchAllAssociative('
            SELECT *
            FROM #__node_term_relationship
            WHERE node_id IN (:node_id)', [
            'node_id' => $nodeIdList,
        ], [
            'node_id' => ConnectionInterface::PARAM_ARRAY_STR,
        ]);
        $result = [];

        foreach ($source as $row) {
            $result[$row['node_id']][$row['type']][] = $row['term_id'];
        }

        return $result;
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
            ->from('#__node', 'tm')
            ->innerJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id')
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
    protected function searchByTitle(array $query): void
    {
        if (! $query['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.title LIKE :tl_title')
            ->setParameter('tl_title', '%' . $query['search'] . '%', PDO::PARAM_STR);
    }

    /**
     * @param array $query
     */
    protected function buildCategory(array $query): void
    {
        if (empty($query['category'])) {
            return;
        }

        if (\is_array($query['category']) === false) {
            $query['category'] = [$query['category']];
        }

        $this->joinTable('node_term_relationship');

        $this->queryBuilder
            ->andWhere('ttr.term_id IN (:ttr_category)')
            ->setParameter('ttr_category', $query['category'], ConnectionInterface::PARAM_ARRAY_STR);
    }

    /**
     * @param array $query
     */
    protected function buildTaxonomy(array $query): void
    {
        if ($query['taxonomy'] === []) {
            return;
        }

        $taxonomy = $query['taxonomy'];
        $relation = $taxonomy['relation'] ?? 'AND';

        unset($taxonomy['relation']);

        $this->queryBuilder
            ->innerJoin('tm', '#__node_term_relationship', 'ttr', 'ttr.node_id = tm.id')
            ->innerJoin('ttr', '#__term', 'tt', 'tt.id = ttr.term_id');

        $wheres = [];

        foreach ($taxonomy as $key => $tax) {
            $tax = array_merge([
               'taxonomy' => null,
               'field'    => 'term_id',
               'terms'    => [],
           ], $tax);

            if (! $tax['taxonomy']) {
                continue;
            }

            $field = 'tt.id';

            if (\is_array($tax['terms'])) {
                $wheres[] = "{$tax['field']} IN (:{$tax['field']}_{$key})";
                $this->queryBuilder->setParameter($tax['field'] . '_' . $key, $tax['terms'], Connection::PARAM_STR_ARRAY);
            } else {
                $wheres[] = "{$field} = :{$tax['field']}_{$key}";
                $this->queryBuilder->setParameter($tax['field'] . '_' . $key, $tax['terms'], PDO::PARAM_STR);
            }

            $wheres[] = 'tt.type = :tt_type_' . $key;
            $this->queryBuilder->setParameter('tt_type_' . $key, $tax['taxonomy'], PDO::PARAM_STR);
        }

        $this->queryBuilder->andWhere('(' . implode(" $relation ", $wheres) . ')');
    }

    /**
     * @param array $query
     */
    protected function buildNodeType(array $query): void
    {
        $types    = \is_array($query['node_type'])      ? $query['node_type']      : [ $query['node_type'] ];
        $typesNot = \is_array($query['node_type__not']) ? $query['node_type__not'] : [ $query['node_type__not'] ];

        if ($query['node_type'] !== null && $query['node_type'] !== 'any' && $types !== []) {
            $this->queryBuilder
                ->andWhere('tm.type IN (:tm_type_in)')
                ->setParameter('tm_type_in', $types, Connection::PARAM_STR_ARRAY);
        }

        if ($query['node_type__not'] !== null && $typesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.type NOT IN (:tm_type_not_in)')
                ->setParameter('tm_type_not_in', $typesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    /**
     * @param array $query
     */
    protected function buildNodeStatus(array $query): void
    {
        $statuses    = \is_array($query['node_status'])      ? $query['node_status']      : [ $query['node_status'] ];
        $statusesNot = \is_array($query['node_status__not']) ? $query['node_status__not'] : [ $query['node_status__not'] ];

        if ($query['node_status'] !== null && $query['node_status'] !== 'any' && $statuses !== []) {
            $this->queryBuilder
                ->andWhere('tm.status IN (:tm_status_in)')
                ->setParameter('tm_status_in', $statuses, Connection::PARAM_STR_ARRAY);
        }

        if ($query['node_status__not'] !== null && $statusesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.status NOT IN (:tm_status_not_in)')
                ->setParameter('tm_status_not_in', $statusesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    /**
     * @param array $query
     */
    protected function buildDate(array $query): void
    {
        if (! $query['published_after']) {
            return;
        }

        if ($query['published_after'] === 'now') {
            $query['published_after'] = date('Y-m-d H:i:s');
        } else {
            $query['published_after'] = strtotime('Y-m-d H:i:s', $query['published_after']);
        }

        $this->queryBuilder
            ->andWhere('tm.published_at <= :tm_published_after')
            ->setParameter('tm_published_after', $query['published_after'], PDO::PARAM_STR);

        if ($query['published_to']) {
            if ($query['published_to'] === 'now') {
                $query['published_to'] = date('Y-m-d H:i:s');
            } else {
                $query['published_to'] = strtotime('Y-m-d H:i:s', $query['published_to']);
            }

            $this->queryBuilder
                ->andWhere('IF(tm.published_to IS NULL, 1, tm.published_to >= :tm_published_to) = 1')
                ->setParameter('tm_published_to', $query['published_to'], PDO::PARAM_STR);
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
        if ($query['order_hierarchical']) {
            $this->queryBuilder->addOrderBy('tm.level', 'ASC');
        }

        if ($query['order_by']) {
            $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
        }
    }

    protected function joinTable(string $table): void
    {
        if (isset($this->joinedTables[$table])) {
            return;
        }

        switch ($table) {
            case 'node_term_relationship':
                $this->queryBuilder->innerJoin('tm', '#__node_term_relationship', 'ttr', 'ttr.node_id = tm.id');
                $this->joinedTables[$table] = true;
                break;
        }
    }
}
