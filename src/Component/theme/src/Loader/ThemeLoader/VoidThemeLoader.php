<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Loader\ThemeLoader;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class VoidThemeLoader implements ThemeLoaderInterface
{
    public function load(): ThemeInterface
    {
        throw new \RuntimeException('You must provide theme loader which always returns any theme!');
    }
}
