<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Loader\ThemeLoader;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeLoaderInterface
{
    /**
     * @return ThemeInterface
     */
    public function load(): ThemeInterface;
}
