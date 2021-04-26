<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Query\Menu;

use PDO;
use Tulia\Cms\Menu\Application\Query\Finder\AbstractQuery;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalQuery extends AbstractQuery
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    public function execute(array $query): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('tm.*')
            ->from('#__menu', 'tm');

        if ($query['id']) {
            $qb->andWhere('tm.id = :tm_id')
                ->setParameter('tm_id', $query['id'], PDO::PARAM_STR)
                ->setMaxResults(1);
        }

        if ($query['website']) {
            $qb->andWhere('tm.website_id = :tm_website_id')
                ->setParameter('tm_website_id', $query['website'], PDO::PARAM_STR);
        }

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param array $result
     *
     * @return int
     */
    protected function getCountFromResult(array $result): int
    {
        return (int) ($result[0]['count'] ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMenuItems(array $query): array
    {
        $qb = $this->connection->createQueryBuilder()
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
            ->setParameter('tm_menu_id', $query['id'], PDO::PARAM_STR)
            ->setParameter('tl_locale', $query['locale'], PDO::PARAM_STR)
        ;

        if ($query['visibility']) {
            $qb
                ->andWhere('COALESCE(tl.visibility, tm.visibility, 0) = :tl_visibility')
                ->setParameter('tl_visibility', $query['visibility'], PDO::PARAM_INT);
        }

        if ($query['order_hierarchical']) {
            $qb->addOrderBy('tm.level', 'ASC');
        }

        if ($query['order_by']) {
            $qb->addOrderBy($query['order_by'], $query['order_dir']);
        }

        return $qb->execute()->fetchAllAssociative();
    }
}
