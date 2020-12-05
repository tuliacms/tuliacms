<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\ContentSecurityPolicy\EventListener;

use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseHeaderRegistrator
{
    /**
     * @var ContentSecurityPolicyInterface
     */
    protected $csp;

    /**
     * @param ContentSecurityPolicyInterface $csp
     */
    public function __construct(ContentSecurityPolicyInterface $csp)
    {
        $this->csp = $csp;
    }

    /**
     * @param ResponseEvent $event
     */
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
