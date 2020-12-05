<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Tulia\Component\Routing\Exception\RoutingException;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RequestMatcher
{
    /**
     * @var RouterInterface
     */
    protected $matcher;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param RouterInterface $matcher
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(RouterInterface $matcher, CurrentWebsiteInterface $currentWebsite)
    {
        $this->matcher = $matcher;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * @param RequestEvent $event
     *
     * @throws RoutingException
     * @throws \Exception
     */
    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            return;
        }

        $result = $this->matcher->match(urldecode($request->getContentPath()));

        $request->attributes->add($result);
    }
}
