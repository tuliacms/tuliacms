<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Component\Routing\RouteCollectionInterface;
use Tulia\Framework\Kernel\Event\BootstrapEvent;

/**
 * @author Adam Banaszkiewicz
 */
class RouteCollector implements EventSubscriberInterface
{
    protected RouteCollectionInterface $collection;

    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BootstrapEvent::class => [
                ['collect', 1000],
            ],
        ];
    }

    public function collect(): void
    {
        $collection = $this->collection;

        if (tulia_installed()) {
            include __DIR__ . '/../../Resources/config/routing.php';
        } else {
            include __DIR__ . '/../../../../../Installator/Infrastructure/Framework/Resources/config/routing.php';
        }
    }
}
