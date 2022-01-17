<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\LayoutType;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutTypeRepository
{
    private LayoutTypeStorageInterface $layoutTypeStorage;

    /*public function __construct(LayoutTypeStorageInterface $layoutTypeStorage)
    {
        $this->layoutTypeStorage = $layoutTypeStorage;
    }*/

    public function insert(LayoutType $layoutType): void
    {

    }

    public function update(LayoutType $layoutType): void
    {

    }

    public function delete(LayoutType $layoutType): void
    {

    }
}
