<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Activator;

use Tulia\Component\Theme\Exception\MissingThemeException;

/**
 * @author Adam Banaszkiewicz
 */
interface ActivatorInterface
{
    /**
     * @param string $name
     *
     * @throws MissingThemeException
     */
    public function activate(string $name): void;
}
