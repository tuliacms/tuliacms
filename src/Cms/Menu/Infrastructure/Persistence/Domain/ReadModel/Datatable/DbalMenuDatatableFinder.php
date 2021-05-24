<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Tulia\Cms\Menu\Ports\Infrastructure\Persistence\ReadModel\Datatable\MenuDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalMenuDatatableFinder extends AbstractDatatableFinder implements MenuDatatableFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigurationKey(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns(): array
    {
        return [
            'id' => [
                'selector' => 'tm.id',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'name' => [
                'selector' => 'tm.name',
                'label' => 'name',
                'sortable' => true,
                'view' => '@backend/menu/menu/parts/datatable/name.tpl',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__menu', 'tm')
            ->where('tm.website_id = :website_id')
            ->setParameter('website_id', $this->currentWebsite->getId(), PDO::PARAM_STR)
        ;

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/menu/menu/parts/datatable/links/edit-link.tpl',
            'items' => '@backend/menu/menu/parts/datatable/links/items-link.tpl',
            'delete' => '@backend/menu/menu/parts/datatable/links/delete-link.tpl',
        ];
    }
}
