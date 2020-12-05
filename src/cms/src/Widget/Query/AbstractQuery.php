<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query;

use Exception;
use Tulia\Cms\Widget\Query\Model\Collection;
use Tulia\Cms\Widget\Query\Model\Widget;
use Tulia\Cms\Widget\Query\Exception\QueryException;

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
             * @param null|string|array|string[]
             */
            'id__not_in' => null,
            /**
             * @param null|int|bool
             */
            'visibility' => null,
            /**
             * @param null|string|array|string[]
             */
            'space' => null,
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
            'order_by' => 'name',
            'order_dir' => 'DESC',
            /**
             * If query have to count rows, please provide the column name
             * which should be counted. If column to count does not matter,
             * provide boolean `true` and Query does care about column name.
             */
            'count' => null,
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
            /**
             * Search widgets by names and titles.
             *
             * @param null|string
             */
            'search' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function query(array $query): Collection
    {
        $base = $this->getBaseQueryArray();
        $base['visibility'] = 1;
        $criteria = array_merge($base, $query);

        return $this->createCollection($this->execute($criteria), $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function queryRaw(array $query): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $query);

        return $this->createCollection($this->execute($criteria), $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $query): int
    {
        $base = $this->getBaseQueryArray();
        $base['visibility'] = 1;
        $base['count']      = true;

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
    protected function createCollection(array $result, array $criteria): Collection
    {
        $collection = new Collection();

        if ($result === []) {
            return $collection;
        }

        $reset = $this->shouldResetTranslationWhenIsDefaultLocaleRow($criteria);

        try {
            foreach ($result as $row) {
                if ($reset) {
                    $row['translated'] = true;
                }

                $row['styles'] = json_decode($row['styles'], true);
                $row['payload'] = json_decode($row['payload'], true);
                $row['payload_localized'] = json_decode($row['payload_localized'], true);
                $collection->append(Widget::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found widgets: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    private function shouldResetTranslationWhenIsDefaultLocaleRow(array $criteria): bool
    {
        return $criteria['locale'] === $criteria['default_locale'];
    }
}
