<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
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
            'name' => [
                'selector' => 'COALESCE(tl.name, tm.name)',
                'label' => 'name',
                'sortable' => true,
                'view' => '@backend/forms/parts/datatable/name.tpl',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $filters = [
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
        ];

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $filters['translated'] = [
                'label' => 'translated',
                'type' => 'yes_no',
                'selector' => 'IF(ISNULL(tl.name), 0, 1)'
            ];
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__form', 'tm')
            ->leftJoin('tm', '#__form_lang', 'tl', 'tm.id = tl.form_id AND tl.locale = :locale')
            ->where('tm.website_id = :website_id')
            ->setParameter('website_id', $this->currentWebsite->getId(), PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->select('IF(ISNULL(tl.name), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/forms/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/forms/parts/datatable/links/delete-link.tpl',
        ];
    }
}
