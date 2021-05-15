<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query;

use Exception;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\TermMetadataEnum;
use Tulia\Cms\Taxonomy\Query\Model\Collection;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractQuery implements QueryInterface
{
    protected MetadataFinder $metadataFinder;

    /**
     * @param array $result
     *
     * @return int
     */
    abstract protected function getCountFromResult(array $result): int;

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
             * Search for term with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'id' => null,
            /**
             * Search for terms that are not with provided IDs list.
             *
             * @param null|string|array
             */
            'id__not_in' => null,
            /**
             * Search for terms that are with provided IDs list.
             *
             * @param null|string|array
             */
            'id__in' => null,
            /**
             * Search for children of given parentd IDs. Searches only for the next
             * level - never infinite deep!
             *
             * @param null|string|array
             */
            'children_of' => null,
            /**
             * Search for term with given slug.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
             * @param null|string
             */
            'slug' => null,
            /**
             * List of term types to search.
             * If not provided, search for all types.
             *
             * @param null|string|array
             */
            'taxonomy_type' => [],
            /**
             * If taxonomy_type is empty, and taxonomy_type__not is provided, query returns all
             * terms that are not any of given type.
             *
             * @param null|string|array
             */
            'taxonomy_type__not' => [],
            /**
             * @param null|int|bool
             */
            'visibility' => null,
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
            'order_by' => 'tl.name',
            'order_dir' => 'DESC',
            /**
             * In case the taxonomy_type supports `hierarchical` and set this value to
             * true, ordering use `level` column to sort terms like tree.
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
             * Locale of the term to fetch.
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
        $base['taxonomy_type'] = 'category';
        $base['visibility']    = 1;

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
        $base['taxonomy_type'] = 'category';
        $base['visibility']    = 1;
        $base['count']         = true;

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

        $metadata = $this->metadataFinder->findAllAggregated(TermMetadataEnum::TYPE, array_column($result, 'id'));

        try {
            foreach ($result as $row) {
                $row['metadata'] = $metadata[$row['id']] ?? [];

                $collection->append(Term::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found terms: '.$e->getMessage(), 0, $e);
        }

        return $collection;
    }
}
