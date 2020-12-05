<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Item;

/**
 * @author Adam Banaszkiewicz
 */
interface LoaderInterface
{
    public function load(Item $item): void;
}
