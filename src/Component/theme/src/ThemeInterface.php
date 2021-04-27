<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeInterface
{
    public function hasConfig(): bool;

    public function getConfig(): ConfigurationInterface;

    public function setConfig(ConfigurationInterface $config): void;

    public function getParent(): ?string;
}
