<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Logout;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * @author Adam Banaszkiewicz
 */
class LogoutListener implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => ['logout', 100],
        ];
    }

    public function logout(LogoutEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('homepage')));
    }
}
