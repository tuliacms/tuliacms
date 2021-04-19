<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item;

use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Item;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;
use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\Enum\MetadataEnum;
use Tulia\Cms\Metadata\Metadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository
{
    protected ConnectionInterface $connection;
    protected DbalPersister $persister;
    protected CurrentWebsiteInterface $currentWebsite;
    protected HydratorInterface $hydrator;
    protected SyncerInterface $metadata;

    public function __construct(
        ConnectionInterface $connection,
        DbalPersister $persister,
        CurrentWebsiteInterface $currentWebsite,
        HydratorInterface $hydrator,
        SyncerInterface $metadata
    ) {
        $this->connection      = $connection;
        $this->persister       = $persister;
        $this->currentWebsite  = $currentWebsite;
        $this->hydrator        = $hydrator;
        $this->metadata        = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function findItems(AggregateId $id): array
    {
        $source = $this->connection->fetchAll('
            SELECT
                tm.*,
                tl.locale,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.visibility, tm.visibility) AS visibility
            FROM #__menu_item AS tm
            LEFT JOIN #__menu_item_lang AS tl
                ON tm.id = tl.menu_item_id AND tl.locale = :locale
            WHERE tm.menu_id = :menu_id
            ORDER BY tm.position ASC, tm.level ASC', [
            'menu_id' => $id->getId(),
            'locale'  => $this->currentWebsite->getLocale()->getCode()
        ]);
        $items = [];

        foreach ($source as $item) {
            $items[$item['id']] = $this->hydrator->hydrate(
                [
                    'id'       => new ItemId($item['id']),
                    'menuId'   => $item['menu_id'] ? new AggregateId($item['menu_id']) : null,
                    'position' => (int) $item['position'],
                    'parentId' => $item['parent_id'] ? new ItemId($item['parent_id']) : null,
                    'level'    => (int) $item['level'],
                    'type'     => $item['type'],
                    'identity' => $item['identity'],
                    'hash'     => $item['hash'],
                    'target'   => $item['target'],
                    'locale'   => $item['locale'] ?? $this->currentWebsite->getLocale()->getCode(),
                    'name'     => $item['name'],
                    'visibility' => (bool) $item['visibility'],
                    'metadata' => $this->metadata->all(MetadataEnum::MENUITEM_GROUP, $id->getId()),
                ],
                Item::class
            );
        }

        return $items;
    }

    /**
     * @param Item $item
     *
     * @throws \Throwable
     */
    public function save(Item $item): void
    {
        $data = $this->extract($item);

        $this->persister->save($data, $this->currentWebsite->getDefaultLocale()->getCode());
        $this->metadata->push(new Metadata($data['metadata']), MetadataEnum::MENUITEM_GROUP, $data['id']);
    }

    /**
     * @param ItemId $id
     */
    public function delete(ItemId $id): void
    {
        $this->connection->delete('#__menu_item_lang', ['menu_item_id' => $id->getId()]);
        $this->connection->delete('#__menu_item', ['id' => $id->getId()]);

        $this->metadata->delete(
            MetadataEnum::MENUITEM_GROUP,
            $id->getId()
        );
    }

    public function extract(Item $item): array
    {
        $data = $this->hydrator->extract($item);

        $data['id'] = $data['id']->getId();

        if ($data['menu']) {
            $data['menu'] = $data['menu']->getId()->getId();
        }

        if ($data['parent']) {
            $data['parent'] = $data['parent']->getId()->getId();
        }

        if (empty($data['locale'])) {
            $data['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        $data['visibility'] = $data['visibility'] ? 1 : 0;

        return $data;
    }
}
