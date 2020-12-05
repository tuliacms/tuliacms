<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Exception;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Collection;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;

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
     * @param array $query
     *
     * @return array
     */
    abstract protected function fetchMenuItems(array $query): array;

    /**
     * {@inheritdoc}
     */
    abstract public function execute(array $query): array;

    /**
     * @return array
     */
    public function getBaseQueryArray(): array
    {
        return [
            /**
             * Search for menu with given ID.
             * If provided, Query searches only for ONE record (LIMIT 1).
             *
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
             *
             * @param null|string
             */
            'website' => null,
            /**
             * Locale of the menu/menu_items to fetch.
             */
            'locale' => 'en_US',
            /**
             * Visibility of menu items.
             *
             * @param null|int|bool
             */
            'visibility' => 1,
            /**
             * Tells whether or not to fetch also menu items.
             *
             * @param bool
             */
            'fetch_items' => true,
        ];
    }

    /**
     * @param array $query
     *
     * @return Collection
     *
     * @throws QueryException
     */
    public function query(array $query): Collection
    {
        $base = $this->getBaseQueryArray();
        $base['visibility'] = 1;

        $query = array_merge($base, $query);

        return $this->createCollection($this->execute($query), $query);
    }

    /**
     * @param array $query
     *
     * @return Collection
     *
     * @throws QueryException
     */
    public function queryRaw(array $query): Collection
    {
        $query = array_merge($this->getBaseQueryArray(), $query);

        return $this->createCollection($this->execute($query), $query);
    }

    /**
     * @param array $result
     *
     * @return Collection
     *
     * @throws QueryException
     */
    protected function createCollection(array $result, array $query): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        if ($query['fetch_items']) {
            $items = $this->fetchMenuItems($query);
        } else {
            $items = [];
        }

        try {
            foreach ($result as $row) {
                $row['items'] = [];

                foreach ($items as $item) {
                    if ($item['menu_id'] === $row['id']) {
                        $row['items'][] = $item;
                    }
                }

                $collection->append(Menu::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found menus: '.$e->getMessage(), 0, $e);
        }

        return $collection;
    }
}
