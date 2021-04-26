<?php

namespace Tulia\Component\Hooking\Tests\Unit;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Tulia\Component\Hooking\Tests\Unit\TestCase;
use Tulia\Component\Hooking\Event\HookActionEvent;
use Tulia\Component\Hooking\Event\HookFilterEvent;
use Tulia\Component\Hooking\Hooker;

class HookerTest extends TestCase
{
    public function testDoActionShouldReturnContent()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return '111';
        });
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return '222';
        }, 10);
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return '333';
        }, -10);

        $this->assertSame($hooker->doAction('event-name'), '222111333');
    }

    public function testDoActionWithArgumentShouldReturnContentWithThisArgument()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return $event->get('arg').'111';
        });
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return $event->get('arg').'222';
        }, 10);
        $hooker->registerAction('event-name', function (HookActionEvent $event) {
            return $event->get('arg').'333';
        }, -10);

        $this->assertSame($hooker->doAction('event-name', [ 'arg' => 'val' ]), 'val222val111val333');
    }

    public function testDoEmptyFilterShouldReturnContent()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'111';
        });
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'222';
        }, 10);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'333';
        }, -10);

        $this->assertSame($hooker->doFilter('event-name'), '222111333');
    }

    public function testDoFilterShouldReturnContent()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'111';
        });
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'222';
        }, 10);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.'333';
        }, -10);

        $this->assertSame($hooker->doFilter('event-name', 'value'), 'value222111333');
    }

    public function testDoFilterWithArgumentShouldReturnContentWithThisArgument()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.$event->get('arg').'111';
        });
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.$event->get('arg').'222';
        }, 10);
        $hooker->registerFilter('event-name', function ($value, HookFilterEvent $event) {
            return $value.$event->get('arg').'333';
        }, -10);

        $this->assertSame($hooker->doFilter('event-name', null, [ 'arg' => 'val' ]), 'val222val111val333');
    }

    public function testDoActionShouldCallNewlyAddedSubscriberAfterAnotherDoActionCall()
    {
        $hooker = new Hooker(new EventDispatcher);
        $hooker->addSubscriber($firstSubscriber);
        $this->assertSame($hooker->doAction('first-action'), 'first-action-value');

        $hooker->addSubscriber($secondSubscriber);
        $this->assertSame($hooker->doAction('second-action'), 'second-action-value');
    }
}
