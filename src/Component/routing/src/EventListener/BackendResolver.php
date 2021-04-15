<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

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
            RequestEvent::class => ['handle', 9800],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $parameters = [];
        $parameters['_content_path']   = $request->attributes->get('_content_path');
        $parameters['_backend_prefix'] = $this->currentWebsite->getBackendPrefix();

        $parameters = $this->resolveBackend($parameters);

        $request->attributes->add($parameters);
    }

    protected function resolveBackend(array $parameters): array
    {
        $parameters['_is_backend'] = false;

        if (strpos($parameters['_content_path'], $parameters['_backend_prefix']) === 0) {
            $parameters['_is_backend'] = true;
            //$parameters['_content_path'] = substr($parameters['_content_path'], \strlen($parameters['_backend_prefix']));
        }

        return $parameters;
    }
}
