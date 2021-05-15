<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Exception;
use PDO;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\Model\Menu;
use Tulia\Cms\Menu\Domain\Metadata\Item\Enum\MetadataEnum;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;

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
             * Search for menu with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             * @param null|string
             */
            'id' => null,
            /**
             * This field has higher priority than order_by and order_dir.
             * Allows to define custom sort option.
             */
            'order_by' => 'position',
            'order_dir' => 'ASC',
            'order_hierarchical' => true,
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

    public function query(array $criteria): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->setDefaults($criteria);

        if ($criteria['limit']) {
            $this->queryBuilder->setMaxResults($criteria['limit']);
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

        $items = [];
        $metadata = [];

        if ($criteria['fetch_items']) {
            $items = $this->fetchMenuItems($criteria);
            $metadata = $this->metadataFinder->findAllAggregated(MetadataEnum::MENUITEM_GROUP, array_column($items, 'id'));
        }

        try {
            foreach ($result as $row) {
                $row['items'] = [];

                foreach ($items as $item) {
                    if ($item['menu_id'] === $row['id']) {
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
        $qb = $this->queryBuilder->getConnection()->createQueryBuilder()
            ->select('
                tm.*,
                tl.locale,
                IF(ISNULL(tl.name), 0, 1) AS translated,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.visibility, tm.visibility) AS visibility
            ')
            ->from('#__menu_item', 'tm')
            ->leftJoin('tm', '#__menu_item_lang', 'tl', 'tm.id = tl.menu_item_id AND tl.locale = :tl_locale')
            ->andWhere('tm.menu_id = :tm_menu_id')
            ->setParameter('tm_menu_id', $criteria['id'], PDO::PARAM_STR)
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR)
        ;

        if ($criteria['visibility']) {
            $qb
                ->andWhere('COALESCE(tl.visibility, tm.visibility, 0) = :tl_visibility')
                ->setParameter('tl_visibility', $criteria['visibility'], PDO::PARAM_INT);
        }

        if ($criteria['order_hierarchical']) {
            $qb->addOrderBy('tm.level', 'ASC');
        }

        if ($criteria['order_by']) {
            $qb->addOrderBy($criteria['order_by'], $criteria['order_dir']);
        }

        return $qb->execute()->fetchAllAssociative();
    }
}
