<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function buildHierarchy(string $id): HierarchyInterface;

    public function buildHtml(string $id): string;
}
