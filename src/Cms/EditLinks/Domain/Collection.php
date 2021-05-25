<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Domain;

/**
 * @author Adam Banaszkiewicz
 */
class Collection
{
    protected array $links = [];

    public function add(string $name, array $link): void
    {
        $this->links[$name] = $link;
    }

    public function remove(string $name): void
    {
        unset($this->links[$name]);
    }

    public function getAll(): array
    {
        foreach ($this->links as $key => $link) {
            $this->links[$key] = array_merge([
                'label' => '',
                'link' => '',
                'priority' => 0,
            ], $link);
        }

        usort($this->links, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        return $this->links;
    }
}
