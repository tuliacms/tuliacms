<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder;

use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface HtmlBuilderInterface
{
    /**
     * @param HierarchyInterface $hierarchy
     *
     * @return string
     */
    public function build(HierarchyInterface $hierarchy): string;
}
