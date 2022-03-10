<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    public function getTheme(): ThemeInterface;

    public function getStorage(): StorageInterface;

    /**
     * @return ThemeInterface[]
     */
    public function getThemes(): iterable;

    public function getResolver(): ResolverAggregateInterface;

    public function getLoader(): ThemeLoaderInterface;
}
