<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared;

/**
 * Sorts array of arrays. An array inside must contain following indexes:
 *   - id - ID of element
 *   - level - Level of hierarchy
 *   - parent_id - ID of parent element
 *
 * @author Adam Banaszkiewicz
 */
class ArraySorter
{
    protected array $source = [];

    protected array $options = [];

    protected array $result = [];

    public function __construct(array $source = [], array $options = [])
    {
        $this->source  = $source;
        $this->options = array_merge([
            'flat_result' => true,
        ], $options);
    }

    public function sort(): array
    {
        foreach ($this->source as $item) {
            $level = (int) $item['level'];

            if ($level === 0) {
                if ($this->options['flat_result']) {
                    $this->result[] = $item;
                    $this->sortFlat($item);
                } else {
                    $item['children'] = $this->sortHierarhy($item);
                    $this->result[] = $item;
                }
            }
        }

        return $this->result;
    }

    private function sortFlat(array $item): void
    {
        foreach ($this->source as $sItem) {
            if ($sItem['parent_id'] === $item['id']) {
                $this->result[] = $sItem;
                $this->sortFlat($sItem);
            }
        }
    }

    private function sortHierarhy(array $item): array
    {
        $children = [];

        foreach ($this->source as $sItem) {
            if ($sItem['parent_id'] === $item['id']) {
                $sItem['children'] = $this->sortHierarhy($sItem);
                $children[] = $sItem;
            }
        }

        return $children;
    }
}
