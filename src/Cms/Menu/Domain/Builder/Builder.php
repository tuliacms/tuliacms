<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder;

use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    protected HierarchyBuilderInterface $hierarchyBuilder;

    protected HtmlBuilderInterface $htmlBuilder;

    public function __construct(HierarchyBuilderInterface $hierarchyBuilder, HtmlBuilderInterface $htmlBuilder)
    {
        $this->hierarchyBuilder = $hierarchyBuilder;
        $this->htmlBuilder = $htmlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildHierarchy(string $id): HierarchyInterface
    {
        return $this->hierarchyBuilder->build($id);
    }

    /**
     * {@inheritdoc}
     */
    public function buildHtml(string $id): string
    {
        return $this->htmlBuilder->build($this->buildHierarchy($id));
    }
}
