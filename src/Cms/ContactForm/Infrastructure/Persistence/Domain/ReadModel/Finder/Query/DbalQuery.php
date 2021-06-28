<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Finder\Query;

use Doctrine\DBAL\Connection;
use Exception;
use PDO;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Domain\ReadModel\Finder\Query\AbstractDbalQuery;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractDbalQuery
{
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
            'limit' => 1,
        ];
    }

    public function query(array $criteria): Collection
    {
        $criteria = array_merge($this->getBaseQueryArray(), $criteria);
        $criteria = $this->filterCriteria($criteria);

        $this->searchById($criteria);
        $this->searchByName($criteria);
        $this->setDefaults($criteria);
        $this->buildOffset($criteria);
        $this->buildOrderBy($criteria);

        if ($criteria['website']) {
            $this->queryBuilder
                ->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $criteria['website'], PDO::PARAM_STR);
        }

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

        if ($criteria['fetch_fields']) {
            $fields = $this->fetchFields(array_column($result, 'id'), $criteria);
        } else {
            $fields = [];
        }

        try {
            foreach ($result as $row) {
                if (isset($fields[$row['id']])) {
                    foreach ($fields[$row['id']] as $field) {
                        $row['fields'][] = Field::buildFromArray($field);
                    }
                }

                $row['receivers'] = @ (array) json_decode($row['receivers'], true);

                $collection->append(Form::buildFromArray($row));
            }
        } catch (Exception $e) {
            throw new QueryException('Exception during create colection of found forms: ' . $e->getMessage(), 0, $e);
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
            $this->queryBuilder->select([
                'tm.*',
                'tl.locale',
                'IF(ISNULL(tl.name), 0, 1) AS translated',
                'COALESCE(tl.name, tm.name) AS name',
                'COALESCE(tl.subject, tm.subject) AS subject',
                'COALESCE(tl.message_template, tm.message_template) AS message_template',
                'COALESCE(tl.fields_view, tm.fields_view) AS fields_view',
                'COALESCE(tl.fields_template, tm.fields_template) AS fields_template',
            ]);
        }

        $this->queryBuilder
            ->from('#__form', 'tm')
            ->leftJoin('tm', '#__form_lang', 'tl', 'tm.id = tl.form_id AND tl.locale = :tl_locale')
            ->setParameter('tl_locale', $criteria['locale'], PDO::PARAM_STR)
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

    protected function searchByName(array $criteria): void
    {
        if (! $criteria['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('COALESCE(tl.name, tm.name) LIKE :tl_name')
            ->setParameter('tl_name', '%' . $criteria['search'] . '%', PDO::PARAM_STR);
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
        if ($criteria['order_by']) {
            $this->queryBuilder->addOrderBy($criteria['order_by'], $criteria['order_dir']);
        }
    }

    protected function fetchFields(array $ids, array $criteria): array
    {
        $fields = $this->queryBuilder->getConnection()->fetchAllAssociative("
            SELECT
                tm.name,
                tm.type,
                tm.type_alias,
                tm.form_id,
                COALESCE(tl.locale, :locale) AS locale,
                COALESCE(tl.options, tm.options) AS options
            FROM #__form_field AS tm
            LEFT JOIN #__form_field_lang AS tl
                ON tl.form_id = :form_id AND tl.name = tm.name AND tl.locale = :locale
            WHERE tm.form_id = :form_id", [
            'form_id' => $ids,
            'locale' => $criteria['locale'],
        ], [
            'form_id' => ConnectionInterface::PARAM_ARRAY_STR
        ]);

        $result = [];

        foreach ($fields as $key => $val) {
            $fields[$key]['options'] = @ (array) json_decode($fields[$key]['options'], true);

            $result[$val['form_id']][] = $fields[$key];
        }

        return $result;
    }
}
