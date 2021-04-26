<?php

declare(strict_types=1);

namespace Tulia\Framework\Http\Session;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class SessionRegistrator implements EventSubscriberInterface
{
    protected SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['register', 2000],
            ],
        ];
    }

    public function register(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->hasSession() && $request->hasPreviousSession()) {
            return;
        }

        $request->setSession($this->session);
    }
}
