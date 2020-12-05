<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Activator;

/**
 * @author Adam Banaszkiewicz
 */
interface ActivatorInterface
{
    /**
     * @param string $name
     */
    public function activate(string $name): void;
}
