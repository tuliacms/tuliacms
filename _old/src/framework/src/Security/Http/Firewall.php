<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http;

use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class Firewall
{
    private $map;
    private $dispatcher;

    /**
     * @param FirewallMapInterface $map
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(FirewallMapInterface $map, EventDispatcherInterface $dispatcher)
    {
        $this->map = $map;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // register listeners for this firewall
        $listeners = $this->map->getListeners($event->getRequest());

        $authenticationListeners = $listeners[0];
        $logoutListener = $listeners[2];

        $authenticationListeners = function () use ($authenticationListeners, $logoutListener) {
            $accessListener = null;

            foreach ($authenticationListeners as $listener) {
                if ($listener instanceof AccessListener) {
                    $accessListener = $listener;

                    continue;
                }

                yield $listener;
            }

            if (null !== $logoutListener) {
                yield $logoutListener;
            }

            if (null !== $accessListener) {
                yield $accessListener;
            }
        };

        $this->callListeners($event, $authenticationListeners());
    }

    /**
     * @param RequestEvent $event
     * @param iterable $listeners
     */
    protected function callListeners(RequestEvent $event, iterable $listeners): void
    {
        foreach ($listeners as $listener) {
            $listener($event);

            if ($event->hasResponse()) {
                break;
            }
        }
    }
}
