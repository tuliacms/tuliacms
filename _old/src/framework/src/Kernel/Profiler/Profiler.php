<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Profiler;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler as SymfonyProfiler;

/**
 * @author Adam Banaszkiewicz
 */
class Profiler extends SymfonyProfiler
{
    /**
     * @param iterable|array|DataCollectorInterface[] $collectors
     */
    public function setCollectors(iterable $collectors): void
    {
        $this->set(iterator_to_array($collectors));
    }
}
