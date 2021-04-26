<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared;

/**
 * Sorts array of objects. An object must contain following methods:
 * getId() - ID of element
 * getLevel() - Level of hierarchy
 * getParentId() - ID of parent element
 *
 * @author Adam Banaszkiewicz
 */
class ObjectSorter
{
    /**
     * @var array
     */
    protected $source = [];

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @param array $source
     */
    public function __construct(array $source = [])
    {
        $this->source = $source;
    }

    /**
     * @return array
     */
    public function sort(): array
    {
        foreach ($this->source as $item) {
            $level = (int) $item->getLevel();
            if ($level === 0) {
                $this->result[] = $item;
                $this->sortForItem($item);
            }
        }

        return $this->result;
    }

    /**
     * @param object $item
     */
    private function sortForItem(object $item): void
    {
        foreach ($this->source as $sItem) {
            if ($sItem->getParentId() === $item->getId()) {
                $this->result[] = $sItem;
                $this->sortForItem($sItem);
            }
        }
    }
}
