<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query;

use Exception;
use Tulia\Cms\ContactForms\Query\Model\Collection;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Cms\ContactForms\Query\Exception\QueryException;

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
     * @param string $id
     *
     * @return array
     */
    abstract protected function fetchFields(string $id, array $criteria): array;

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
            'order_by' => 'id',
            'order_dir' => 'DESC',
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
            /**
             * Whether or not to fetch forms fields too.
             */
            'fetch_fields' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function query(array $query): Collection
    {
        $base = $this->getBaseQueryArray();
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
        $base['count'] = true;

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
     * @param array $criteria
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
                $row['receivers'] = json_decode($row['receivers'], true);

                if ($reset) {
                    $row['translated'] = true;
                }

                $form = Form::buildFromArray($row);

                if ($criteria['fetch_fields']) {
                    $form->setFields($this->fetchFields($form->getId(), $criteria));
                }

                $collection->append($form);
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found nodes: ' . $e->getMessage(), 0, $e);
        }

        return $collection;
    }

    private function shouldResetTranslationWhenIsDefaultLocaleRow(array $criteria): bool
    {
        return $criteria['locale'] === $criteria['default_locale'];
    }
}
