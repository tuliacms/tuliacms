<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Query\Item;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @var null|string
     */
    private $menuId;

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

    public function setMenuId(string $menuId): void
    {
        $this->menuId = $menuId;
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
            ->from('#__menu_item', 'tm')
            ->addSelect('tm.menu_id')
            ->leftJoin('tm', '#__menu_item_lang', 'tl', 'tm.id = tl.menu_item_id AND tl.locale = :locale')
            ->where('tm.menu_id = :menu_id')
            ->setParameter('menu_id', $this->menuId, PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addOrderBy('tm.position', 'ASC')
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.name), 0, 1) AS translated');
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
                $this->router->generate('backend.menu.item.edit', ['menuId' => $row['menu_id'], 'id' => $row['id']]),
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
        $editLink = $this->router->generate('backend.menu.item.edit', ['menuId' => $row['menu_id'], 'id' => $row['id']]);
        $delete = $this->translator->trans('deleteWidget', [], 'widgets');

        return [
            'main' => '<a href="' . $editLink . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a href="" class="dropdown-item-with-icon dropdown-item-danger" title="' . $delete . '"><i class="dropdown-icon fas fa-times"></i> ' . $delete . '</a>',
        ];
    }
}
