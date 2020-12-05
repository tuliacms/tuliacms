<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer;

use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Exception\MissingAssetException;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * Loads default frontend customizer assets when customizer
 * mode is enabled in Request.
 *
 * @author Adam Banaszkiewicz
 */
class AssetsLoader
{
    /**
     * @var AssetterInterface
     */
    protected $assetter;

    /**
     * @var DetectorInterface
     */
    protected $detector;

    /**
     * @param AssetterInterface $assetter
     * @param DetectorInterface $detector
     */
    public function __construct(AssetterInterface $assetter, DetectorInterface $detector)
    {
        $this->assetter = $assetter;
        $this->detector = $detector;
    }

    /**
     * @param RequestEvent $event
     *
     * @throws MissingAssetException
     */
    public function handle(RequestEvent $event): void
    {
        if ($this->detector->isCustomizerMode()) {
            $this->assetter->require('customizer.front');
        }
    }
}
