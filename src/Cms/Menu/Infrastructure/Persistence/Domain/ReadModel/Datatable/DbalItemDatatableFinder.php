<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Datatable\ItemDatatableFinderInterface;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalItemDatatableFinder extends AbstractDatatableFinder implements ItemDatatableFinderInterface
{
    private TranslatorInterface $translator;

    private ?string $menuId = null;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection, $currentWebsite);

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
                'selector' => 'COALESCE(tl.name, tm.name)',
                'label' => 'name',
                'view' => '@backend/menu/item/parts/datatable/name.tpl',
            ],
            'visibility' => [
                'selector' => 'COALESCE(tl.visibility, tm.visibility)',
                'label' => 'visibility',
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
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__menu_item', 'tm')
            ->addSelect('tm.menu_id, tm.level, tm.parent_id')
            ->leftJoin('tm', '#__menu_item_lang', 'tl', 'tm.id = tl.menu_item_id AND tl.locale = :locale')
            ->where('tm.menu_id = :menu_id')
            ->andWhere('tm.is_root = 0')
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
        return $this->sort($result);
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/menu/item/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/menu/item/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function sort(array $items, int $level = 1, string $parent = Item::ROOT_ID): array
    {
        $result = [];

        foreach ($items as $item) {
            $item['level'] = (int) $item['level'];

            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $level + 1, $item['id']);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
