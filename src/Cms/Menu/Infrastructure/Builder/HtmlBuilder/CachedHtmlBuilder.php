<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Domain\Builder\HtmlBuilder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CachedHtmlBuilder implements HtmlBuilderInterface
{
    private HtmlBuilderInterface $builder;
    private TagAwareCacheInterface $menuCache;

    public function __construct(HtmlBuilderInterface $builder, TagAwareCacheInterface $menuCache)
    {
        $this->builder = $builder;
        $this->menuCache = $menuCache;
    }

    public function build(HierarchyInterface $hierarchy): string
    {
        return $this->menuCache->get(sprintf('menu_html_%s', $hierarchy->getId()), function (ItemInterface $item) use ($hierarchy) {
            $item->tag('menu_html');
            $item->tag(sprintf('menu_%s', $hierarchy->getId()));

            return $this->builder->build($hierarchy);
        });
    }
}
