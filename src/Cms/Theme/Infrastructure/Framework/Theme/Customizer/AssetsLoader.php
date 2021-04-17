<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer;

use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Exception\MissingAssetException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/**
 * Loads default frontend customizer assets when customizer
 * mode is enabled in Request.
 *
 * @author Adam Banaszkiewicz
 */
class AssetsLoader implements EventSubscriberInterface
{
    protected AssetterInterface $assetter;
    protected DetectorInterface $detector;

    public function __construct(AssetterInterface $assetter, DetectorInterface $detector)
    {
        $this->assetter = $assetter;
        $this->detector = $detector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'handle',
        ];
    }

    public function handle(RequestEvent $event): void
    {
        if ($this->detector->isCustomizerMode()) {
            $this->assetter->require('customizer.front');
        }
    }
}
