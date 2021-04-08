<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    /**
     * @return ThemeInterface
     */
    public function getTheme(): ThemeInterface;

    /**
     * @return StorageInterface
     */
    public function getStorage(): StorageInterface;
}
