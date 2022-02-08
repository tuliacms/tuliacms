<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Breadcrumbs;

/**
 * @author Adam Banaszkiewicz
 */
class Breadcrumbs implements BreadcrumbsInterface
{
    protected $breadcrumbs = [];

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->breadcrumbs);
    }

    /**
     * {@inheritdoc}
     */
    public function push($href, $label): void
    {
        $this->breadcrumbs[] = [
            'href'  => (string) $href,
            'label' => (string) $label
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function pop(): array
    {
        return array_pop($this->breadcrumbs);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $crumbs): void
    {
        $this->breadcrumbs = [];

        foreach ($crumbs as $crumb) {
            if (isset($crumb['label'], $crumb['href'])) {
                $this->push($crumb['label'], $crumb['href']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unshift($href, $label): void
    {
        array_unshift($this->breadcrumbs, [
            'href'  => (string) $href,
            'label' => (string) $label
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function shift(): array
    {
        return array_shift($this->breadcrumbs);
    }

    /**
     * {@inheritdoc}
     */
    public function render(): string
    {
        $result = '<ol class="breadcrumb">';
        $total  = count($this->breadcrumbs);

        foreach ($this->breadcrumbs as $key => $crumb) {
            $result .= '<li class="breadcrumb-item '.($total === ($key + 1) ? 'active' : '').'">
                <a href="'.htmlspecialchars($crumb['href']).'" title="'.htmlspecialchars($crumb['label'], ENT_QUOTES).'">
                    '.htmlspecialchars($crumb['label'], ENT_QUOTES).'
                </a>
            </li>';
        }

        return $result.'</ol>';
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->breadcrumbs);
    }
}
