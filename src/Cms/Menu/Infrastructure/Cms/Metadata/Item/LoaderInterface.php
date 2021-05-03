<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item as DomainModel;

/**
 * @author Adam Banaszkiewicz
 */
interface LoaderInterface
{
    /**
     * @param DomainModel $item
     */
    public function load($item): void;
}
