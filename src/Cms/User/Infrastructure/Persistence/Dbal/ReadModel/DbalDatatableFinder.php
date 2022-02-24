<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\ReadModel;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;

/**
 * @author Adam Banaszkiewicz
 */
class DbalDatatableFinder extends AbstractDatatableFinder
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
            'email' => [
                'selector' => 'tm.email',
                'label' => 'email',
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
            ->select('tm.email, COALESCE(ua.value, ual.value, "") AS name')
            ->from('#__user', 'tm')
            ->leftJoin('tm', '#__user_attribute', 'ua', "ua.owner_id = tm.id AND ua.name = 'name'")
            ->leftJoin('ua', '#__user_attribute_lang', 'ual', 'ua.id = ual.attribute_id')
            ->groupBy('tm.id')
            ->addGroupBy('ual.value')
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
