<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Component\Routing\RouterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RequestMatcher implements EventSubscriberInterface
{
    protected RouterInterface $matcher;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(RouterInterface $matcher, CurrentWebsiteInterface $currentWebsite)
    {
        $this->matcher = $matcher;
        $this->currentWebsite = $currentWebsite;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['onRequest', 200]
            ],
        ];
    }

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
