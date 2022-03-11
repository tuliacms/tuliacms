<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Exception;
use PDO;
use Tulia\Cms\Attributes\Domain\ReadModel\Service\AttributesFinder;
use Tulia\Cms\Menu\Domain\ReadModel\Model\Menu;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
    private AttributesFinder $metadataFinder;

    public function __construct(QueryBuilder $queryBuilder, AttributesFinder $metadataFinder)
    {
        parent::__construct($queryBuilder);
        $this->metadataFinder = $metadataFinder;
    }

    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for menu with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             * @param null|string
             */
            'id' => null,
            /**
             * Search for rows in the website. Given null search in all websites.
             * @param null|string
             */
            'website' => null,
            /**
             * Locale of the menu/menu_items to fetch.
             */
            'locale' => 'en_US',
            /**
             * Visibility of menu items.
             * @param null|int|bool
             */
            'visibility' => 1,
            /**
             * Tells whether or not to fetch also menu items.
             * @param bool
             */
            'fetch_items' => true,
            'limit' => null,
        ];
    }

    public function query(array $criteria, string $scope): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->setDefaults($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
        }

        $this->callPlugins($criteria);

        return $this->createCollection($this->queryBuilder->execute()->fetchAllAssociative(), $scope, $criteria);
    }

    protected function createCollection(array $result, string $scope, array $criteria): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $items = [];
        $metadata = [];

        if ($criteria['fetch_items']) {
            $items = $this->fetchMenuItems($criteria);
            $metadata = $this->metadataFinder->findAllAggregated('menu_item', $scope, array_column($items, 'id'));
        }

        try {
            foreach ($result as $row) {
                $row['items'] = [];

                foreach ($items as $item) {
                    if ($item['menu_id'] === $row['id'] && ! $item['is_root']) {
                        $item['metadata'] = $metadata[$item['id']] ?? [];
                        $row['items'][] = $item;
                    }
                }

                $collection->append(Menu::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found menus: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    protected function setDefaults(array $criteria): void
    {
        $qb = $this->queryBuilder
            ->select('tm.*')
            ->from('#__menu', 'tm');

        if ($criteria['id']) {
            $qb->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', $criteria['id'], PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($criteria['website']) {
            $qb->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $criteria['website'], PDO::PARAM_STR);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMenuItems(array $criteria): array
    {
        $where = ['1 = 1'];
        $parameters = [
            'menu_id' => $criteria['id'],
            'locale'  => $criteria['locale'],
        ];

        if ($criteria['visibility']) {
            $where[] = 'COALESCE(tl.visibility, tm.visibility, 0) = :tl_visibility';
            $parameters['tl_visibility'] = $criteria['visibility'];
        }

        $where = implode(' AND ', $where);

        return $this->queryBuilder->getConnection()->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    menu_id,
    parent_id,
    position,
    level,
    is_root,
    type,
    identity,
    hash,
    target,
    name,
    visibility,
    path
) AS (
        SELECT
            id,
            menu_id,
            parent_id,
            position,
            level,
            is_root,
            type,
            identity,
            hash,
            target,
            name,
            visibility,
            CONCAT(name, '/') as path
        FROM #__menu_item
        WHERE
            is_root = 1
            AND menu_id = :menu_id
    UNION ALL
        SELECT
            tm.id,
            tm.menu_id,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.is_root,
            tm.type,
            tm.identity,
            tm.hash,
            tm.target,
            COALESCE(tl.name, tm.name) AS name,
            COALESCE(tl.visibility, tm.visibility) AS visibility,
            CONCAT(tp.path, tm.name, '/') AS path
        FROM tree_path AS tp
        INNER JOIN #__menu_item AS tm
            ON tp.id = tm.parent_id
        LEFT JOIN #__menu_item_lang AS tl
            ON tm.id = tl.menu_item_id AND tl.locale = :locale
        WHERE
            {$where}
)
SELECT * FROM tree_path
ORDER BY position, path", $parameters);
    }
}
