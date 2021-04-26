<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder;

use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function buildHierarchy(string $id): HierarchyInterface;
    public function buildHtml(string $id): string;
}
