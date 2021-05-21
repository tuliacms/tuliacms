<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Query;

use PDO;
use Symfony\Component\Routing\RouterInterface;
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
    private RouterInterface $router;
    private TranslatorInterface $translator;
    private WidgetRegistryInterface $widgetRegistry;
    private ManagerInterface $themeManager;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        RouterInterface $router,
        TranslatorInterface $translator,
        WidgetRegistryInterface $widgetRegistry,
        ManagerInterface $themeManager
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->router = $router;
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
                'html_attr' => [
                    'class' => 'col-title',
                ],
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
            ->addSelect('tm.widget_id')
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
    public function prepareResult(array $result): array
    {
        $missingLocale = $this->translator->trans('missingTranslationInThisLocale');
        $types = $this->collectWidgetsNames();

        foreach ($result as &$row) {
            $badges = '';

            if (isset($row['translated']) && $row['translated'] !== '1') {
                $badges .= '<span class="badge badge-info" data-toggle="tooltip" title="' . $missingLocale . '"><i class="dropdown-icon fas fa-language"></i></span> ';
            }

            $widgetType = $types[$row['widget_id']] ?? $row['widget_id'];

            $row['name'] = sprintf(
                '<a href="%2$s" title="%1$s" class="link-title">%4$s %1$s %3$s</a>',
                $row['name'],
                $this->router->generate('backend.widget.edit', ['id' => $row['id']]),
                '<br /><span class="slug">' . $this->translator->trans('widgetType', [], 'widgets') . ': ' . $widgetType . '</span>',
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
        $editLink = $this->router->generate('backend.widget.edit', ['id' => $row['id']]);
        $delete = $this->translator->trans('deleteWidget', [], 'widgets');

        return [
            'main' => '<a href="' . $editLink . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a href="" class="dropdown-item-with-icon dropdown-item-danger" title="' . $delete . '"><i class="dropdown-icon fas fa-times"></i> ' . $delete . '</a>',
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
