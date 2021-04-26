<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget\Registry;

use Tulia\Component\Widget\Exception\WidgetNotFoundException;
use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetRegistryInterface
{
    public function add(WidgetInterface $widget): void;
    public function remove(WidgetInterface $widget): void;

    /**
     * @param string $id
     *
     * @return WidgetInterface
     *
     * @throws WidgetNotFoundException
     */
    public function get(string $id): WidgetInterface;
    public function has(string $id): bool;

    /**
     * @return array|WidgetInterface[]
     */
    public function all(): iterable;
}
