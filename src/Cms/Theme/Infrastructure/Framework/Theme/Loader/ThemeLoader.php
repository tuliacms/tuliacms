<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeLoader implements ThemeLoaderInterface
{
    private StorageInterface $storage;
    private CurrentWebsiteInterface $currentWebsite;
    private string $configFilename;

    public function __construct(
        StorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        string $configFilename
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->configFilename = $configFilename;
    }

    public function load(): ThemeInterface
    {
        $themesByWebsite = include $this->configFilename;

        if (
            isset($themesByWebsite[$this->currentWebsite->getId()])
            && $this->storage->has($themesByWebsite[$this->currentWebsite->getId()])
        ) {
            return $this->storage->get($themesByWebsite[$this->currentWebsite->getId()]);
        }

        return new DefaultTheme();
    }
}
