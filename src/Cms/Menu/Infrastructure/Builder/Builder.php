<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder;

use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    /**
     * @var HierarchyBuilderInterface
     */
    protected $hierarchyBuilder;

    /**
     * @var HtmlBuilderInterface
     */
    protected $htmlBuilder;

    /**
     * @param HierarchyBuilderInterface $hierarchyBuilder
     * @param HtmlBuilderInterface $htmlBuilder
     */
    public function __construct(HierarchyBuilderInterface $hierarchyBuilder, HtmlBuilderInterface $htmlBuilder)
    {
        $this->hierarchyBuilder = $hierarchyBuilder;
        $this->htmlBuilder      = $htmlBuilder;
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
