<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Query\Menu;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Symfony\Component\Routing\RouterInterface;
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
                'selector' => 'tm.name',
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
    public function prepareResult(array $result): array
    {
        foreach ($result as &$row) {
            $row['name.raw'] = $row['name'];
            $row['name'] = sprintf(
                '<a href="%2$s" title="%1$s" class="link-title">%1$s</a>',
                $row['name'],
                $this->router->generate('backend.menu.item.list', ['menuId' => $row['id']])
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        $itemsLink = $this->router->generate('backend.menu.item.list', ['menuId' => $row['id']]);
        $itemsList = $this->translator->trans('menuItems', [], 'menu');
        $delete    = $this->translator->trans('deleteMenu', [], 'menu');

        return [
            'main' => '<a href="#" data-toggle="modal" data-target="#modal-menu-edit" data-element-name="' . $row['name.raw'] . '" data-element-id="' . $row['id'] . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a href="' . $itemsLink . '" class="dropdown-item-with-icon" title="' . $itemsList . '"><i class="dropdown-icon fas fa-bars"></i> ' . $itemsList . '</a>',
            '<a href="" class="dropdown-item-with-icon dropdown-item-danger" title="' . $delete . '"><i class="dropdown-icon fas fa-times"></i> ' . $delete . '</a>',
        ];
    }
}
