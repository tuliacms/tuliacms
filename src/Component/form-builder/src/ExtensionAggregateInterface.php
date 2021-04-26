<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionAggregateInterface
{
    public function aggregate(): array;
}
