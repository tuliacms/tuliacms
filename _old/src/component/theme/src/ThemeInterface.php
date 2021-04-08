<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeInterface
{
    /**
     * @return bool
     */
    public function hasConfig(): bool;

    /**
     * @return ConfigurationInterface
     */
    public function getConfig(): ConfigurationInterface;

    /**
     * @param ConfigurationInterface $config
     */
    public function setConfig(ConfigurationInterface $config): void;
}
