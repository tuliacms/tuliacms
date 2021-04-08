<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Query;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Filter\ComparisonOperatorsEnum;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConnectionInterface $connection
     * @param CurrentWebsiteInterface $currentWebsite
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->router = $router;
        $this->translator = $translator;
    }

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
                'comparisons' => ComparisonOperatorsEnum::all(),
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
    public function prepareResult(array $result): array
    {
        $missingLocale = $this->translator->trans('missingTranslationInThisLocale');

        foreach ($result as &$row) {
            $badges = '';

            if (isset($row['translated']) && $row['translated'] !== '1') {
                $badges .= '<span class="badge badge-info" data-toggle="tooltip" title="' . $missingLocale . '"><i class="dropdown-icon fas fa-language"></i></span> ';
            }

            $row['name'] = sprintf(
                '<a href="%2$s" title="%1$s" class="link-title">%3$s %1$s</a>',
                $row['name'],
                $this->router->generate('backend.form.edit', ['id' => $row['id']]),
                $badges
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        $editLink = $this->router->generate('backend.form.edit', ['id' => $row['id']]);

        return [
            'main' => '<a href="' . $editLink . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a href="" class="dropdown-item-with-icon dropdown-item-danger"><i class="dropdown-icon fas fa-times"></i> Usuń</a>',
        ];
    }
}
