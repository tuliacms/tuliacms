<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme\Assetter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationAssetsLoader implements EventSubscriberInterface
{
    protected BaseLoader $loader;

    public function __construct(BaseLoader $loader)
    {
        $this->loader = $loader;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['handle', 2000],
        ];
    }

    public function handle(ViewEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_is_backend')) {
            return;
        }

        $this->loader->load();
    }
}
