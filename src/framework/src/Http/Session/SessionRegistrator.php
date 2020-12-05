<?php

declare(strict_types=1);

namespace Tulia\Framework\Http\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class SessionRegistrator
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param RequestEvent $event
     */
    public function register(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->hasSession() && $request->hasPreviousSession()) {
            return;
        }

        $request->setSession($this->session);
    }
}
