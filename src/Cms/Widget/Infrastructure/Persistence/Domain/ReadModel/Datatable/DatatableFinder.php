<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
{
    private TranslatorInterface $translator;

    private WidgetRegistryInterface $widgetRegistry;

    private ManagerInterface $themeManager;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        TranslatorInterface $translator,
        WidgetRegistryInterface $widgetRegistry,
        ManagerInterface $themeManager
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->translator = $translator;
        $this->widgetRegistry = $widgetRegistry;
        $this->themeManager = $themeManager;
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
        $context = [
            'widget_names' => $this->collectWidgetsNames(),
        ];

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
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/widget/parts/datatable/name.tpl',
                'view_context' => $context,
            ],
            'space' => [
                'selector' => 'tm.space',
                'label' => 'space',
                'translation_domain' => 'widgets',
                'sortable' => true,
                'value_translation' => $this->collectSpacesList(),
            ],
            'visibility' => [
                'selector' => 'COALESCE(tl.visibility, tm.visibility)',
                'label' => 'visibility',
                'sortable' => true,
                'html_attr' => ['class' => 'text-center'],
                'value_translation' => [
                    '1' => $this->translator->trans('visible'),
                    '0' => $this->translator->trans('invisible'),
                ],
                'value_class' => [
                    '1' => 'text-success',
                    '0' => 'text-danger',
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
            'name' => [
                'label' => 'name',
                'type' => 'text',
            ],
            'space' => [
                'label' => 'space',
                'translation_domain' => 'widgets',
                'type' => 'single_select',
                'selector' => 'tm.space',
                'choices' => $this->collectSpacesList(),
            ],
            'visibility' => [
                'label' => 'visibility',
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
            ->from('#__widget', 'tm')
            ->addSelect('tm.widget_type')
            ->leftJoin('tm', '#__widget_lang', 'tl', 'tm.id = tl.widget_id AND tl.locale = :locale')
            ->where('tm.website_id = :website_id')
            ->setParameter('website_id', $this->currentWebsite->getId(), PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/widget/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/widget/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function collectSpacesList(): array
    {
        $theme  = $this->themeManager->getTheme();
        $spaces = $theme->hasConfig() ? $theme->getConfig()->getRegisteredWidgetSpaces() : [];
        $spacesList = [];

        foreach ($spaces as $space) {
            $spacesList[$space['name']] = $this->translator->trans($space['label'], [], $space['translation_domain']);
        }

        return $spacesList;
    }

    private function collectWidgetsNames(): array
    {
        $widgetsNames = [];

        foreach ($this->widgetRegistry->all() as $widget) {
            $info = $widget->getInfo();

            $widgetsNames[$widget->getId()] = $this->translator->trans($info['name'], [], $info['translation_domain'] ?? null);
        }

        return $widgetsNames;
    }
}
