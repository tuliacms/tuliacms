<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query;

use Exception;
use Tulia\Cms\Node\Domain\Enum\TermTypeEnum;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Node\Query\Exception\QueryException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractQuery implements QueryInterface
{
    /**
     * @param array $result
     *
     * @return int
     */
    abstract protected function getCountFromResult(array $result): int;

    /**
     * @param array $nodesIds
     *
     * @return array
     */
    abstract protected function fetchTerms(array $nodeIdList): array;

    /**
     * {@inheritdoc}
     */
    abstract public function execute(array $query): array;

    /**
     * {@inheritdoc}
     */
    abstract public function countFoundRows(): int;

    /**
     * {@inheritdoc}
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
             *
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
             *
             * @param null|string
             */
            'website' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function query(array $query): Collection
    {
        $base = $this->getBaseQueryArray();
        $base['node_type']   = 'page';
        $base['node_status'] = [ 'published' ];
        $base['published_after'] = 'now';
        $base['published_to']    = 'now';

        return $this->createCollection($this->execute(array_merge($base, $query)));
    }

    /**
     * {@inheritdoc}
     */
    public function queryRaw(array $query): Collection
    {
        return $this->createCollection($this->execute(array_merge($this->getBaseQueryArray(), $query)));
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $query): int
    {
        $base = $this->getBaseQueryArray();
        $base['node_type']   = 'page';
        $base['node_status'] = [ 'published' ];
        $base['published_after'] = 'now';
        $base['published_to']    = 'now';
        $base['count']       = true;

        return $this->getCountFromResult($this->execute(array_merge($base, $query)));
    }

    /**
     * {@inheritdoc}
     */
    public function countRaw(array $query): int
    {
        $base = $this->getBaseQueryArray();
        $base['count'] = true;

        return $this->getCountFromResult($this->execute(array_merge($base, $query)));
    }

    /**
     * @param array $result
     *
     * @return Collection
     *
     * @throws QueryException
     */
    protected function createCollection(array $result): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $terms = $this->fetchTerms(array_column($result, 'id'));

        try {
            foreach ($result as $row) {
                if (isset($terms[$row['id']][TermTypeEnum::MAIN][0])) {
                    $row['category'] = $terms[$row['id']][TermTypeEnum::MAIN][0];
                }

                $collection->append(Node::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: '.$e->getMessage(), 0, $e);
        }

        return $collection;
    }
}
