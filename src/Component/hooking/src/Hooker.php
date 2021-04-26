<?php

declare(strict_types=1);

namespace Tulia\Component\Hooking;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Component\Hooking\Event\HookActionEvent;
use Tulia\Component\Hooking\Event\HookFilterEvent;
use Tulia\Component\Hooking\Subscriber\SubscriberInterface;
use Tulia\Component\Hooking\EventDispatcher\EventDispatcherWrapper;

/**
 * @author Adam Banaszkiewicz
 */
class Hooker implements HookerInterface
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = new EventDispatcherWrapper($dispatcher);
    }

    public function registerAction(string $action, callable $callable, int $priority = 0): void
    {
        $this->dispatcher->addListener('hooker.action.'.$action, $callable, $priority);
    }

    public function registerFilter(string $filter, callable $callable, int $priority = 0): void
    {
        $this->dispatcher->addListener('hooker.filter.'.$filter, $callable, $priority);
    }

    public function unregisterAction(string $action, callable $callable)
    {

    }

    public function unregisterFilter(string $filter, callable $callable)
    {

    }

    public function doAction($action, array $arguments = [])
    {
        if ($action instanceof HookActionEvent) {
            $event = $action;
        } else {
            $event = new HookActionEvent($action, $arguments);
        }

        $this->dispatcher->dispatchAction('hooker.action.'.$action, $event);

        return $event->getContent();
    }

    public function doFilter($filter, $content = null, array $arguments = [])
    {
        if ($filter instanceof HookFilterEvent) {
            $event = $filter;
        } else {
            $event = new HookFilterEvent($filter, $arguments, $content);
        }

        $this->dispatcher->dispatchFilter('hooker.filter.'.$filter, $event);

        return $event->getContent();
    }
}
