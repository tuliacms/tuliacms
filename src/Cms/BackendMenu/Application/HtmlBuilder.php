<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Application;

use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Application\Registry\ItemRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class HtmlBuilder implements HtmlBuilderInterface
{
    protected BuilderHelperInterface $helper;
    protected array $builders = [];
    protected array $defaults = [
        /**
         * IDs of opened elements.
         */
        'opened' => [],
    ];

    /**
     * @param BuilderHelperInterface $helper
     */
    public function __construct(BuilderHelperInterface $helper, iterable $builders = [])
    {
        $this->helper = $helper;

        foreach ($builders as $builder) {
            $this->add($builder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(BuilderInterface $builder): void
    {
        $this->builders[] = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $params = []): string
    {
        $params = array_merge($this->defaults, $params);

        $registry = new ItemRegistry();

        foreach ($this->builders as $builder) {
            $builder->build($registry);
        }

        $elements = $registry->all();
        $result = '<ul>';

        foreach ($elements as $key => $element) {
            if (isset($elements[$key]['active']) === false) {
                if ($element['link'] && $this->helper->isHomepage() === false && $this->helper->isInPath($element['link'])) {
                    $elements[$key]['active'] = true;
                } else {
                    $elements[$key]['active'] = false;
                }
            }

            if (\in_array($element['id'], $params['opened'], true)) {
                $elements[$key]['opened'] = true;
            } else {
                $elements[$key]['opened'] = false;
            }
        }

        foreach ($elements as $element) {
            if ($element['parent'] === null) {
                $result .= $this->buildElement($element, $elements);
            }
        }

        return $result . '</ul>';
    }

    /**
     * @param array $element
     * @param array $elements
     *
     * @return string
     */
    private function buildElement(array $element, array $elements): string
    {
        if ($element['type'] === 'link') {
            $class = '';
            $icon = '';

            $children = $this->findChildren($element['id'], $elements);

            if ($element['icon']) {
                $class .= ' item-has-icon';
                $icon = '<span class="item-icon ' . $element['icon'] . '"></span>';
            }

            if ($children !== []) {
                $class .= ' has-dropdown';
            }

            if ($element['active'] || $this->anyChildActive($children)) {
                $class .= ' active dropdown-opened';
            }

            if ($element['opened']) {
                $class .= ' dropdown-opened';
            }

            $attributes = [];
            $attributes['data-priority'] = $element['priority'];
            $attributes['id'] = 'aside-menuitem-' . $element['id'];
            $attributes['class'] = $class;
            $attributes['data-item-id'] = $element['id'];

            $result = '<li ' . $this->buildAttributes($attributes) . '><a href="' . $element['link'] . '" title="' . $element['label'] . '">' . $icon . '<span class="item-label">' . $element['label'] . '</span></a>';

            if ($children !== []) {
                $result .= '<ul class="dropdown">';

                foreach ($children as $child) {
                    $result .= $this->buildElement($child, $elements);
                }

                $result .= '</ul>';
            }

            return $result . '</li>';
        }

        if ($element['type'] === 'section') {
            return '<li class="separator"></li><li data-priority="' . $element['priority'] . '" class="headline">' . $element['label'] . '</li>';
        }

        return '';
    }

    /**
     * @param string $id
     * @param array $elements
     *
     * @return array
     */
    private function findChildren(string $id, array $elements): array
    {
        $children = [];

        foreach ($elements as $element) {
            if ($element['parent'] === $id) {
                $children[] = $element;
            }
        }

        return $children;
    }

    /**
     * @param array $elements
     *
     * @return bool
     */
    private function anyChildActive(array $elements): bool
    {
        foreach ($elements as $element) {
            if ($element['active']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    private function buildAttributes(array $attributes): string
    {
        $attrsHtml = [];

        foreach($attributes as $key => $value) {
            $attrsHtml[] = $key . '="' . $value . '"';
        }

        return implode(' ', $attrsHtml);
    }
}
