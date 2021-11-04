<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term as WriteModelTerm;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    private MetadataFinder $metadataFinder;

    public function __construct(QueryBuilder $queryBuilder, MetadataFinder $metadataFinder)
    {
        parent::__construct($queryBuilder);

        $this->metadataFinder = $metadataFinder;
    }

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
             * Allows to define custom sort option.
             */
            'order' => 'tm.position',
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
            /**
             * Search string. Searching by title with LIKE operator.
             */
            'search' => null,
            /**
             * Locale of the term to fetch.
             */
            'locale' => 'en_US',
            /**
             * Search for rows in the website. Given null search in all websites.
             * @param null|string
             */
            'website' => null,
            /**
             * Is fet to true, result will be sorted hierarchical after fetching.
             * It will works only for full, complete fetched tree.
             */
            'sort_hierarchical' => false,
        ];
    }

    public function query(array $criteria): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchBySlug($criteria);
        $this->searchByName($criteria);
        $this->setDefaults($criteria);
        $this->buildTaxonomyType($criteria);
        $this->buildVisibility($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['website']) {
            $this->queryBuilder
                ->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $criteria['website'], PDO::PARAM_STR);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative(), $criteria);
    }

    protected function createCollection(array $result, array $criteria): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        foreach ($result as $key => $row) {
            $result[$key]['level'] = (int) $row['level'];
            $result[$key]['position'] = (int) $row['position'];
        }

        if ($criteria['sort_hierarchical']) {
            $result = $this->sortHierarchical($result, WriteModelTerm::ROOT_LEVEL + 1);
        }

        $metadata = $this->metadataFinder->findAllAggregated('node', array_column($result, 'id'));

        try {
            foreach ($result as $row) {
                $row['metadata'] = $metadata[$row['id']] ?? [];

                $collection->append(Term::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
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
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.locale, :tl_locale) AS locale
            ');
        }

        $this->queryBuilder
            ->from('#__term', 'tm')
            ->leftJoin('tm', '#__term_lang', 'tl', 'tm.id = tl.term_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR);
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
                ->setParameter('tm_children_of', $ids, Connection::PARAM_STR_ARRAY);
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

        if ($criteria['id__in']) {
            if (\is_array($criteria['id__in']) === false) {
                $ids = [ $criteria['id__in'] ];
            } else {
                $ids = $criteria['id__in'];
            }

            $this->queryBuilder
                ->andWhere('tm.id IN (:tm_id__in)')
                ->setParameter('tm_id__in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function searchBySlug(array $criteria): void
    {
        if (! $criteria['slug']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.slug = :tl_slug OR tm.slug = :tl_slug')
            ->setParameter('tl_slug', $criteria['slug'], PDO::PARAM_STR)
            ->setMaxResults(1);
    }

    protected function searchByName(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.name LIKE :tl_name OR tm.name LIKE :tl_name')
            ->setParameter('tl_name', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
    }

    protected function buildTaxonomyType(array $criteria): void
    {
        $types    = \is_array($criteria['taxonomy_type'])      ? $criteria['taxonomy_type']      : [ $criteria['taxonomy_type'] ];
        $typesNot = \is_array($criteria['taxonomy_type__not']) ? $criteria['taxonomy_type__not'] : [ $criteria['taxonomy_type__not'] ];

        if ($criteria['taxonomy_type'] !== null && $criteria['taxonomy_type'] !== 'any' && $types !== []) {
            $this->queryBuilder
                ->andWhere('tm.type IN (:tm_type_in)')
                ->setParameter('tm_type_in', $types, Connection::PARAM_STR_ARRAY);
        }

        if ($criteria['taxonomy_type__not'] !== null && $typesNot !== []) {
            $this->queryBuilder
                ->andWhere('tm.type NOT IN (:tm_type_not_in)')
                ->setParameter('tm_type_not_in', $typesNot, Connection::PARAM_STR_ARRAY);
        }
    }

    protected function buildVisibility(array $criteria): void
    {
        if (! $criteria['visibility']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('COALESCE(tl.visibility, tm.visibility) = :tl_visibility')
            ->setParameter('tl_visibility', $criteria['visibility'], PDO::PARAM_INT);
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
        if ($criteria['order']) {
            $this->queryBuilder->add('orderBy', $criteria['order'], true);
        }
    }

    private function sortHierarchical(array $items, int $level, string $parent = WriteModelTerm::ROOT_ID): array
    {
        $result = [];

        foreach ($items as $item) {
            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sortHierarchical($items, $level + 1, $item['id']);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
