<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\ViewFilter;

/**
 * @author Adam Banaszkiewicz
 */
interface FilterInterface
{
    /**
     * @param string $view
     *
     * @return array
     */
    public function filter(string $view): array;
}
