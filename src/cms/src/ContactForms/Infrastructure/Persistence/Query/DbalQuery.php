<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Query;

use PDO;
use Exception;
use Tulia\Cms\ContactForms\Query\Exception\QueryException;
use Tulia\Cms\ContactForms\Query\AbstractQuery;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Database\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractQuery
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $query): array
    {
        $this->searchById($query);
        $this->searchByName($query);
        $this->setDefaults($query);
        $this->buildOffset($query);
        $this->buildOrderBy($query);

        if ($query['website']) {
            $this->queryBuilder
                ->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $query['website'], PDO::PARAM_STR);
        }

        return $this->queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * {@inheritdoc}
     */
    public function countFoundRows(): int
    {
        try {
            $result = (clone $this->queryBuilder)
                ->select('COUNT(id) AS count')
                ->setMaxResults(null)
                ->setFirstResult(null)
                ->execute()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new QueryException('Exception during countFoundRows() call: ' . $e->getMessage(), 0, $e);
        }

        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCountFromResult(array $result): int
    {
        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchFields(string $id, array $criteria): array
    {
        if ($criteria['locale'] === $criteria['default_locale']) {
            $fields = $this->queryBuilder->getConnection()->fetchAllAssociative(
                'SELECT * FROM #__form_field WHERE form_id = :form_id',
                [ 'form_id' => $id ]
            );
        } else {
            $fields = $this->queryBuilder->getConnection()->fetchAllAssociative(
                'SELECT * FROM #__form_field_lang WHERE form_id = :form_id AND locale = :locale',
                [ 'form_id' => $id, 'locale'  => $criteria['locale'] ]
            );
        }

        foreach ($fields as $key => $val) {
            $fields[$key]['options'] = @ (array) json_decode($fields[$key]['options'], true);
        }

        return $fields;
    }

    /**
     * @param array $query
     */
    protected function setDefaults(array $query): void
    {
        if ($query['count'] === true) {
            $this->queryBuilder->select('COUNT(tm.id) AS count');
        } elseif ($query['count']) {
            $this->queryBuilder->select('COUNT(' . $query['count'] . ') AS count');
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
            ->setParameter('tl_locale', $query['locale'], PDO::PARAM_STR)
        ;
    }

    /**
     * @param array $query
     */
    protected function searchById(array $query): void
    {
        if ($query['id']) {
            $this->queryBuilder
                ->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', $query['id'], PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($query['id__not_in']) {
            if (\is_array($query['id__not_in']) === false) {
                $ids = [ $query['id__not_in'] ];
            } else {
                $ids = $query['id__not_in'];
            }

            $this->queryBuilder
                ->andWhere('tm.id NOT IN (:tm_id__not_in)')
                ->setParameter('tm_id__not_in', $ids, Connection::PARAM_STR_ARRAY);
        }
    }

    /**
     * @param array $query
     */
    protected function searchByName(array $query): void
    {
        if (! $query['search']) {
            return;
        }

        $this->queryBuilder
            ->andWhere('tl.name LIKE :tl_name')
            ->setParameter('tl_name', '%' . $query['search'] . '%', PDO::PARAM_STR);
    }

    /**
     * @param array $query
     */
    protected function buildOffset(array $query): void
    {
        if ($query['per_page'] && $query['page']) {
            $this->queryBuilder->setFirstResult($query['page'] <= 1 ? 0 : ($query['per_page'] * ($query['page'] - 1)));
        }

        if ($query['per_page']) {
            $this->queryBuilder->setMaxResults($query['per_page']);
        }
    }

    /**
     * @param array $query
     */
    protected function buildOrderBy(array $query): void
    {
        if ($query['order_by']) {
            $this->queryBuilder->addOrderBy($query['order_by'], $query['order_dir']);
        }
    }
}
