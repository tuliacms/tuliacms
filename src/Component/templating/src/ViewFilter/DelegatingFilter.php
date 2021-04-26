<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\ViewFilter;

/**
 * @author Adam Banaszkiewicz
 */
class DelegatingFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private array $filters = [];

    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(string $view): array
    {
        $views = [[]];

        foreach ($this->filters as $filter) {
            $views[] = $filter->filter($view);
        }

        return array_merge(...$views);
    }
}
