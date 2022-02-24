<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Domain\Builder\Hierarchy\HierarchyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CachedHierarchyBuilder implements HierarchyBuilderInterface
{
    private HierarchyBuilderInterface $builder;
    private TagAwareCacheInterface $menuCache;

    public function __construct(HierarchyBuilderInterface $builder, TagAwareCacheInterface $menuCache)
    {
        $this->builder = $builder;
        $this->menuCache = $menuCache;
    }

    public function build(string $id, array $collection = []): HierarchyInterface
    {
        return $this->menuCache->get(sprintf('menu_hierarchy_%s', $id), function (ItemInterface $item) use ($id, $collection) {
            $item->tag('menu_hierarchy');
            $item->tag(sprintf('menu_%s', $id));

            return $this->builder->build($id, $collection);
        });
    }
}
