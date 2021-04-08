<?php

namespace Tulia\Component\Hooking\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

class EventDispatcherWrapper
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    public function dispatchAction($eventName, Event $event)
    {
        $content = $event->getContent();

        $this->dispatch($eventName, $event, function (callable $listener, Event $event, $eventName) use (& $content) {
            if($result = $listener($event, $eventName, $this))
                $content .= $result;
        });

        $event->setContent($content);
    }

    public function dispatchFilter($eventName, Event $event)
    {
        $content = $event->getContent();

        $this->dispatch($eventName, $event, function (callable $listener, Event $event, $eventName) use (& $content) {
            $content = $listener($content, $event, $eventName, $this);
        });

        $event->setContent($content);
    }

    public function dispatch($eventName, Event $event, callable $callable)
    {
        if($listeners = $this->dispatcher->getListeners($eventName))
        {
            foreach($listeners as $listener)
            {
                if($event->isPropagationStopped())
                    break;

                $callable($listener, $event, $eventName);
            }
        }
    }
}
