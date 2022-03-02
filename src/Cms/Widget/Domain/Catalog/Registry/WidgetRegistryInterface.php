<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Registry;

use Tulia\Cms\Widget\Domain\Catalog\Exception\WidgetNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetRegistryInterface
{
    /**
     * @throws WidgetNotFoundException
     */
    public function get(string $id): WidgetInfo;
    public function has(string $id): bool;

    /**
     * @return WidgetInfo[]
     */
    public function all(): iterable;
}
