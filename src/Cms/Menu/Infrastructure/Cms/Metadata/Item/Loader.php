<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item;

use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\Enum\MetadataEnum;
use Tulia\Cms\Metadata\LazyMetadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;

/**
 * @author Adam Banaszkiewicz
 */
class Loader implements LoaderInterface
{
    protected SyncerInterface $syncer;

    public function __construct(SyncerInterface $syncer)
    {
        $this->syncer = $syncer;
    }

    public function load(Item $item): void
    {
        $item->setMetadata(LazyMetadata::create($this->syncer, MetadataEnum::MENUITEM_GROUP, $item->getId()));
    }
}
