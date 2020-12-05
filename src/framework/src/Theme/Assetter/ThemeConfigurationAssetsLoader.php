<?php

declare(strict_types=1);

namespace Tulia\Framework\Theme\Assetter;

use Requtize\Assetter\Exception\MissingAssetException;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;
use Tulia\Framework\Kernel\Event\ViewEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader
{
    /**
     * @var BaseLoader
     */
    protected $loader;

    /**
     * @param BaseLoader $loader
     */
    public function __construct(BaseLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param ViewEvent $event
     *
     * @throws MissingAssetException
     */
    public function handle(ViewEvent $event): void
    {
        if ($event->getRequest()->isBackend()) {
            return;
        }

        $this->loader->load();
    }
}
