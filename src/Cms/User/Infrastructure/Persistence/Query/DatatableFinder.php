<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Query;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
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
            'username' => [
                'selector' => 'tm.username',
                'label' => 'username',
                'sortable' => true,
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/user/user/parts/datatable/name.tpl',
            ],
            'enabled' => [
                'selector' => 'tm.enabled',
                'label' => 'enabled',
                'sortable' => true,
                'value_translation' => [
                    '1' => 'Enabled',
                    '0' => 'Disabled',
                ],
                'value_class' => [
                    '1' => 'text-success',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            'username' => [
                'label' => 'username',
                'type' => 'text',
            ],
            'email' => [
                'label' => 'email',
                'type' => 'text',
                'selector' => 'tm.email',
            ],
            'enabled' => [
                'label' => 'enabled',
                'type' => 'yes_no',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->select('tm.email, uml.value AS name')
            ->from('#__user', 'tm')
            ->leftJoin('tm', '#__user_metadata', 'um', "um.owner_id = tm.id AND um.name = 'name'")
            ->leftJoin('um', '#__user_metadata_lang', 'uml', 'um.id = uml.metadata_id')
            ->groupBy('tm.id')
            ->addGroupBy('uml.value')
        ;

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/user/user/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/user/user/parts/datatable/links/delete-link.tpl',
        ];
    }
}
