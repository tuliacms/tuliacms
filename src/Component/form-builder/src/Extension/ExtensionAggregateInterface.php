<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Extension;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionAggregateInterface
{
    public function aggregate(): array;
}
