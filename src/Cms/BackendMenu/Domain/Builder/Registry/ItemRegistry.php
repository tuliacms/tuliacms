<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder\Registry;

/**
 * @author Adam Banaszkiewicz
 */
class ItemRegistry implements ItemRegistryInterface
{
    protected $items = [];

    protected $defaults = [
        /**
         * Type of item. Can be `item` oraz `section`.
         */
        'type' => 'link',
        /**
         * Label of item. Must be a translation definition.
         */
        'label' => null,
        /**
         * ID of item, to which this element should be inserted.
         * Parent element ID.
         */
        'parent' => null,
        /**
         * Link to page of this element.
         */
        'link' => null,
        /**
         * Order priority. Higher value - higher in list.
         */
        'priority' => 10,
        /**
         * HTML Icon class.
         */
        'icon' => null,
        /**
         * Additional HTML class for LI element.
         */
        'html_class' => null,
    ];

    /**
     * {@inheritdoc}
     */
    public function add(string $id, array $item): void
    {
        $item['id'] = $id;

        $this->items[$id] = array_merge($this->defaults, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): ?array
    {
        return $this->items[$id] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $id): void
    {
        unset($this->items[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        uasort($this->items, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $items): void
    {
        $this->items = $items;
    }
}
