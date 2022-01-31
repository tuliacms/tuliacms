<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Model\Enum\TermTypeEnum;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFinderQuery extends AbstractDbalQuery
{
    private MetadataFinder $metadataFinder;

    protected array $joinedTables = [];

    private ContentTypeRegistry $contentTypeRegistry;

    public function __construct(
        QueryBuilder $queryBuilder,
        MetadataFinder $metadataFinder,
        ContentTypeRegistry $contentTypeRegistry
    ) {
        parent::__construct($queryBuilder);

        $this->metadataFinder = $metadataFinder;
        $this->contentTypeRegistry = $contentTypeRegistry;
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
             * Search for children of given parentd IDs. Searches only for the next
             * level - never infinite deep!
             *
             * @param null|string|array
             */
            'children_of' => null,
            /**
             * Search for node with given slug.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'slug' => null,
            /**
             * List of node types to search.
             * If not provided, search for all types.
             *
             * @param null|string|array
             */
            'node_type' => [],
            /**
             * If node_type is empty, and node_type__not is provided, query returns all
             * nodes that are not any of given type.
             *
             * @param null|string|array
             */
            'node_type__not' => [],
            /**
             * List of node statuses to search.
             * If not provided, search for all statuses.
             *
             * @param null|string|array
             */
            'node_status' => [],
            /**
             * If node_status is empty, and node_status__not is provided, query returns all
             * nodes that are not any of given statuses.
             *
             * @param null|string|array
             */
            'node_status__not' => [],
            /**
             * Limits search results to nodes published in given date or in the past,
             * but not in the future.
             *
             * When using with default query() method, searching is limited to rows
             * published NOW or in past.
             *
             * You can type here any valid MySQL date format.
             *
             * If not provided, search all posts.
             *
             * @param null|string
             */
            'published_after' => null,
            /**
             * Limits search results to nodes which are published to this date or in past,
             * but not in the future.
             *
             * When using with default query() method, searching is limited to rows
             * published to NOW or in future.
             *
             * You can type here any valid MySQL date format.
             *
             * If not provided, search all posts.
             *
             * @param null|string
             */
            'published_to' => null,
            'taxonomy' => [],
            /**
             * Search for nodes in specified category or categories.
             * @param null|string|array
             */
            'category' =>  null,
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
            'order_by' => 'published_at',
            'order_dir' => 'DESC',
            /**
             * In case the node_type supports `hierarchical` and set this value to
             * true, ordering use `level` column to sort nodes like tree.
             */
            'order_hierarchical' => false,
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Search string. Seaching by title with LIKE operator.
             */
            'search' => null,
            /**
             * Locale of the node to fetch.
             */
            'locale' => 'en_US',
            /**
             * Search for rows in the website. Given null search in all websites.
             * @param null|string
             */
            'website' => null,
            'limit' => null,
            /**
             * Posts with defined flag or flags.
             * @param null|string|array
             */
            'flag' => null,
        ];
    }

    public function query(array $criteria): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchBySlug($criteria);
        $this->searchByTitle($criteria);
        $this->setDefaults($criteria);
        $this->buildCategory($criteria);
        $this->buildTaxonomy($criteria);
        $this->buildNodeType($criteria);
        $this->buildNodeStatus($criteria);
        $this->buildNodeFlag($criteria);
        $this->buildDate($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative());
    }

    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $terms = $this->fetchTerms(array_column($result, 'id'));
        $attributes = $this->metadataFinder->findAllAggregated('node', array_column($result, 'id'));

        try {
            foreach ($result as $row) {
                if (isset($terms[$row['id']][TermTypeEnum::MAIN][0])) {
                    $row['category'] = $terms[$row['id']][TermTypeEnum::MAIN][0];
                }

                $row['attributes'] = $attributes[$row['id']] ?? [];
                $row['attributes_info'] = [];
                $row['flags'] = array_filter(explode(',', (string) $row['flags']));

                foreach ($this->contentTypeRegistry->get($row['type'])->getFields() as $name => $field) {
                    if ($field->hasFlag('compilable') === false || isset($row['attributes'][$name]) === false) {
                        continue;
                    }

                    $row['attributes_info'][$name] = [
                        'compilable' => true,
                    ];
                }

                $collection->append(Node::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

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

    protected function setDefaults(array $criteria): void
    {
        if ($criteria['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($criteria['count']) {
            $this->queryBuilder->select('COUNT(' . $criteria['count'] . ') AS count');
        } else {
            $this->queryBuilder->select('
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.locale, :tl_locale) AS locale,
                GROUP_CONCAT(tnhf.flag SEPARATOR \',\') AS flags
            ');
        }

        $this->queryBuilder
            ->from('#__node', 'tm')
            ->leftJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR)
            ->leftJoin('tm', '#__node_has_flag', 'tnhf', 'tm.id = tnhf.node_id')
            ->addGroupBy('tm.id')
        ;
    }

    protected function searchById(array $criteria): void
    {
        if ($criteria['id']) {
            $this->queryBuilder
                ->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', $criteria['id'], PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($criteria['children_of']) {
            if (\is_array($criteria['children_of']) === false) {
                $ids = [ $criteria['children_of'] ];
            } else {
                $ids = $criteria['children_of'];
            }

            $this->queryBuilder
                ->andWhere('tm.parent_id IN (:tm_children_of)')
                ->setParameter('tm_children_of', $ids, \Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['id__not_in']) {
            if (\is_array($criteria['id__not_in']) === false) {
                $ids = [ $criteria['id__not_in'] ];
            } else {
                $ids = $criteria['id__not_in'];
            }

            $this->queryBuilder
                ->andWhere('tm.id NOT IN (:tm_id__not_in)')
                ->setParameter('tm_id__not_in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function searchBySlug(array $criteria): void
    {
        if (! $criteria['slug']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.slug = :tl_slug OR tl.slug = :tl_slug')
            ->setParameter('tl_slug', $criteria['slug'], PDO::PARAM_STR)
            ->setMaxResults(1);
    }

    protected function searchByTitle(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tm.title LIKE :tl_title OR tl.title LIKE :tl_title')
            ->setParameter('tl_title', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
    }

    protected function buildCategory(array $criteria): void
    {
        if (empty($criteria['category'])) {
            return;
        }

        if (\is_array($criteria['category']) === false) {
            $criteria['category'] = [$criteria['category']];
        }

        $this->joinTable('node_term_relationship');

        $this->queryBuilder
            ->andWhere('ttr.term_id IN (:ttr_category)')
            ->setParameter('ttr_category', $criteria['category'], ConnectionInterface::PARAM_ARRAY_STR);
    }

    protected function buildTaxonomy(array $criteria): void
    {
        if ($criteria['taxonomy'] === []) {
            return;
        }

        $taxonomy = $criteria['taxonomy'];
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

    protected function buildNodeType(array $criteria): void
    {
        $types    = \is_array($criteria['node_type'])      ? $criteria['node_type']      : [ $criteria['node_type'] ];
        $typesNot = \is_array($criteria['node_type__not']) ? $criteria['node_type__not'] : [ $criteria['node_type__not'] ];

        if ($criteria['node_type'] !== null && $criteria['node_type'] !== 'any' && $types !== []) {
            $this->queryBuilder
                ->andWhere('tm.type IN (:tm_type_in)')
                ->setParameter('tm_type_in', $types, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['node_type__not'] !== null && $typesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.type NOT IN (:tm_type_not_in)')
                ->setParameter('tm_type_not_in', $typesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildNodeStatus(array $criteria): void
    {
        $statuses    = \is_array($criteria['node_status'])      ? $criteria['node_status']      : [ $criteria['node_status'] ];
        $statusesNot = \is_array($criteria['node_status__not']) ? $criteria['node_status__not'] : [ $criteria['node_status__not'] ];

        if ($criteria['node_status'] !== null && $criteria['node_status'] !== 'any' && $statuses !== []) {
            $this->queryBuilder
                ->andWhere('tm.status IN (:tm_status_in)')
                ->setParameter('tm_status_in', $statuses, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['node_status__not'] !== null && $statusesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.status NOT IN (:tm_status_not_in)')
                ->setParameter('tm_status_not_in', $statusesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildNodeFlag(array $criteria): void
    {
        if (! $criteria['flag']) {
            return;
        }

        if (is_array($criteria['flag']) === false) {
            $criteria['flag'] = [$criteria['flag']];
        }

        $this->queryBuilder
            ->innerJoin('tm', '#__node_has_flag', 'nhf', 'tm.id = nhf.node_id AND nhf.flag IN (:nhf_flag)')
            ->setParameter('nhf_flag', $criteria['flag'], Connection::PARAM_STR_ARRAY)
        ;
    }

    protected function buildDate(array $criteria): void
    {
        if (! $criteria['published_after']) {
            return;
        }

        if ($criteria['published_after'] === 'now') {
            $criteria['published_after'] = date('Y-m-d H:i:s');
        } else {
            $criteria['published_after'] = strtotime('Y-m-d H:i:s', $criteria['published_after']);
        }

        $this->queryBuilder
            ->andWhere('tm.published_at <= :tm_published_after')
            ->setParameter('tm_published_after', $criteria['published_after'], PDO::PARAM_STR);

        if ($criteria['published_to']) {
            if ($criteria['published_to'] === 'now') {
                $criteria['published_to'] = date('Y-m-d H:i:s');
            } else {
                $criteria['published_to'] = strtotime('Y-m-d H:i:s', $criteria['published_to']);
            }

            $this->queryBuilder
                ->andWhere('IF(tm.published_to IS NULL, 1, tm.published_to >= :tm_published_to) = 1')
                ->setParameter('tm_published_to', $criteria['published_to'], PDO::PARAM_STR);
        }
    }

    protected function buildOffset(array $criteria): void
    {
        if ($criteria['per_page'] && $criteria['page']) {
            $this->queryBuilder->setFirstResult($criteria['page'] <= 1 ? 0 : ($criteria['per_page'] * ($criteria['page'] - 1)));
        }

        if ($criteria['per_page']) {
            $this->queryBuilder->setMaxResults($criteria['per_page']);
        }
    }

    protected function buildOrderBy(array $criteria): void
    {
        if ($criteria['order_hierarchical']) {
            $this->queryBuilder->addOrderBy('tm.level', 'ASC');
        }

        if ($criteria['order_by']) {
            $this->queryBuilder->addOrderBy($criteria['order_by'], $criteria['order_dir']);
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
