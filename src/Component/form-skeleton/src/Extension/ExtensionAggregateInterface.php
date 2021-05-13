<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Extension;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionAggregateInterface
{
    public function aggregate(): array;
}
