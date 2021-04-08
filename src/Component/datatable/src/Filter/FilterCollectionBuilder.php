<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Filter;

/**
 * @author Adam Banaszkiewicz
 */
class FilterCollectionBuilder
{
    public function build(array $source, array $filters, array $columns): array
    {
        $result = [];

        foreach ($source as $name => $data) {
            if (isset($filters[$name]) === false) {
                continue;
            }

            $result[$name] = new Filter(
                $name,
                $data['value'],
                $filters[$name]['selector'] ?? $columns[$name]['selector'],
                $data['comparison']
            );
        }

        return $result;
    }
}
