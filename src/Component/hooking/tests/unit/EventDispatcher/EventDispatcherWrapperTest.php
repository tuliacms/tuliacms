<?php

namespace Tulia\Component\Hooking\Tests\Unit\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tulia\Component\Hooking\Tests\Unit\TestCase;
use Tulia\Component\Hooking\Event\HookActionEvent;
use Tulia\Component\Hooking\Event\HookFilterEvent;
use Tulia\Component\Hooking\EventDispatcher\EventDispatcherWrapper;

class EventDispatcherWrapperTest extends TestCase
{
    public function testActionsShouldAddContentInProperPriority()
    {
        $dispatcher = new EventDispatcherWrapper(new EventDispatcher);
        $dispatcher->addListener('event-name', function (HookActionEvent $event) {
            return '111';
        });
        $dispatcher->addListener('event-name', function (HookActionEvent $event) {
            return '222';
        }, 10);
        $dispatcher->addListener('event-name', function (HookActionEvent $event) {
            return '333';
        }, -10);

        $event = new HookActionEvent('event-name', [], 'content');
        $dispatcher->dispatchAction('event-name', $event);

        $this->assertSame($event->getContent(), 'content222111333');
    }

    public function testEmptyActionShouldNotEmptyContent()
    {
        $dispatcher = new EventDispatcherWrapper(new EventDispatcher);
        $dispatcher->addListener('event-name', function (HookActionEvent $event) {
            return '111';
        });
        $dispatcher->addListener('event-name', function (HookActionEvent $event) {
            // Empty action
        }, 10);

        $event = new HookActionEvent('event-name', [], 'content');
        $dispatcher->dispatchAction('event-name', $event);

        $this->assertSame($event->getContent(), 'content111');
    }

    public function testFiltersShouldAddContentInProperPriority()
    {
        $dispatcher = new EventDispatcherWrapper(new EventDispatcher);
        $dispatcher->addListener('event-name', function ($value) {
            return $value.'111';
        });
        $dispatcher->addListener('event-name', function ($value) {
            return $value.'222';
        }, 10);
        $dispatcher->addListener('event-name', function ($value) {
            return $value.'333';
        }, -10);

        $event = new HookFilterEvent('event-name', [], 'content');
        $dispatcher->dispatchFilter('event-name', $event);

        $this->assertSame($event->getContent(), 'content222111333');
    }

    public function testEmptyFilterShouldEmptyContent()
    {
        $dispatcher = new EventDispatcherWrapper(new EventDispatcher);
        $dispatcher->addListener('event-name', function ($value) {
            // Empty filter
        }, 10);

        $event = new HookFilterEvent('event-name', [], 'content');
        $dispatcher->dispatchFilter('event-name', $event);

        $this->assertSame($event->getContent(), null);
    }

    public function testEmptyFilterInMiddleShouldEmptyContentToItsCallButNotFurther()
    {
        $dispatcher = new EventDispatcherWrapper(new EventDispatcher);
        $dispatcher->addListener('event-name', function ($value) {
            return $value.'111';
        });
        $dispatcher->addListener('event-name', function ($value) {
            // Empty filter
        }, 10);

        $event = new HookFilterEvent('event-name', [], 'content');
        $dispatcher->dispatchFilter('event-name', $event);

        $this->assertSame($event->getContent(), '111');
    }
}
