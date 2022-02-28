<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Assetter;

use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Exception\MissingAssetException;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader
{
    private ManagerInterface $manager;
    private AssetterInterface $assetter;

    public function __construct(ManagerInterface $manager, AssetterInterface $assetter)
    {
        $this->manager  = $manager;
        $this->assetter = $assetter;
    }

    /**
     * @throws MissingAssetException
     */
    public function load(): void
    {
        $theme = $this->manager->getTheme();

        if ($theme->getParent() && $this->manager->getStorage()->has($theme->getParent())) {
            $parent = $this->manager->getStorage()->get($theme->getParent());

            $this->manager->getResolver()->resolve($parent);
            $this->loadAssets($parent);
        }

        $this->loadAssets($theme);
    }

    /**
     * @throws MissingAssetException
     */
    private function loadAssets(ThemeInterface $theme): void
    {
        $this->assetter->require(array_keys($theme->getConfig()->all('asset')));
    }
}
