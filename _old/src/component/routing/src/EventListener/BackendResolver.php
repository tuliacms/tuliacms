<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Kernel\Event\BootstrapEvent;

/**
 * @author Adam Banaszkiewicz
 */
class BackendResolver implements EventSubscriberInterface
{
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(CurrentWebsiteInterface $currentWebsite)
    {
        $this->currentWebsite = $currentWebsite;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BootstrapEvent::class => ['handle', 9800],
        ];
    }

    public function handle(BootstrapEvent $event): void
    {
        $request = $event->getRequest();

        $parameters = [];
        $parameters['_content_path']   = $request->getContentPath();
        $parameters['_backend_prefix'] = $this->currentWebsite->getBackendPrefix();

        $parameters = $this->resolveBackend($parameters);

        $request->attributes->add($parameters);
    }

    protected function resolveBackend(array $parameters): array
    {
        $parameters['_is_backend'] = false;

        if (strpos($parameters['_content_path'], $parameters['_backend_prefix']) === 0) {
            $parameters['_is_backend'] = true;
            $parameters['_content_path'] = substr($parameters['_content_path'], \strlen($parameters['_backend_prefix']));
        }

        return $parameters;
    }
}
