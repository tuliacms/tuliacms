<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\ViewFilter;

use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DelegatingFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @param FilterInterface[] $filters
     */
    public function __construct(iterable $filters)
    {
        $this->filters = $filters;
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
