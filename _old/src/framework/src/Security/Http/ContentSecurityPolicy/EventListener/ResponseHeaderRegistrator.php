<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\ContentSecurityPolicy\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseHeaderRegistrator implements EventSubscriberInterface
{
    protected ContentSecurityPolicyInterface $csp;

    public function __construct(ContentSecurityPolicyInterface $csp)
    {
        $this->csp = $csp;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent:: class => [
                ['appendHeadersToResponse', -9999]
            ]
        ];
    }

    public function appendHeadersToResponse(ResponseEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        // Skip for profiler.
        // @todo Change checking to more professional way instead of checking route ;)
        if ($route && strncmp($route, 'profiler', 8) === 0) {
            return;
        }

        $this->csp->appendToResponse($event->getResponse());
    }
}
