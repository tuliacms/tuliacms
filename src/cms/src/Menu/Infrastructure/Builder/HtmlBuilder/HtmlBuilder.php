<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder;

use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\Item;

/**
 * @author Adam Banaszkiewicz
 */
class HtmlBuilder implements HtmlBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(HierarchyInterface $hierarchy): string
    {
        $result = '<ul class="navbar-nav navbar-nav-hoverable mr-auto tulia-navbar">';

        foreach ($hierarchy as $item) {
            if ($item->isRoot()) {
                $result .= $this->buildList($item);
            }
        }

        return $result . '</ul>';
    }

    private function buildList(Item $item): string
    {
        $children = '';

        if ($item->hasChildren()) {
            $children = '<ul class="submenu submenu-level-' . ($item->getLevel() + 1) . '">';

            foreach ($item->getChildren() as $child) {
                $children .= $this->buildList($child);
            }

            $children .= '</ul>';
        }

        $liAttributes = [];
        $liAttributes['class'] = 'nav-item';

        if ($children) {
            $liAttributes['class'] .= ' has-children';
        }

        $aAttributes = [];
        $aAttributes['class'] = 'nav-link';
        $aAttributes['href']  = $item->getLink();
        $aAttributes['title'] = $item->getLabel();

        if ($item->getTarget()) {
            $aAttributes['target'] = $item->getTarget();
        }

        $result = '<li' . $this->buildAttributes($liAttributes) . '>';

        $result .= '<a' . $this->buildAttributes($aAttributes) . '>' . $item->getLabel() . '</a>';
        $result .= $children;

        return $result . '</li>';
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    private function buildAttributes(array $attributes): string
    {
        $result = [];

        foreach ($attributes as $key => $val) {
            $result[] = $key . '="' . $val . '"';
        }

        return ' ' . implode(' ', $result);
    }
}
