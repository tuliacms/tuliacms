<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Http\ContentSecurityPolicy\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Tulia\Cms\Security\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

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
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Skip for profiler.
        // @todo Change checking to more professional way instead of checking route ;)
        if ($route && strncmp($route, '_profiler', 9) === 0) {
            return;
        }
        if ($request->attributes->get('_controller') === 'error_controller') {
            return;
        }

        // @todo Add CSP to response
        //$this->csp->appendToResponse($event->getResponse());
    }
}
