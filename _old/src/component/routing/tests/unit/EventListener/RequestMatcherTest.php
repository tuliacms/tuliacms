<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Tests\Unit\EventListener;

use Mockery;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Routing\Tests\Unit\TestCase;
use Tulia\Component\Routing\EventListener\RequestMatcher;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class RequestMatcherTest extends TestCase
{
    public function testMatchRequest(): void
    {
        $matcher = Mockery::mock(MatcherInterface::class);
        $matcher->shouldReceive('match')->once();

        $attributes = Mockery::mock(ParameterBag::class);
        $attributes->shouldReceive('add')->once();

        $request = Mockery::mock(Request::class);
        $request->attributes = $attributes;
        $request->shouldReceive('getPathInfo')->andReturn('/');
        $request->shouldReceive('getMethod')->andReturn('GET');

        $event = Mockery::mock(RequestEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);

        $requestMatcher = new RequestMatcher($matcher);
        $requestMatcher->onRequest($event);

        $this->assertTrue(true);
    }
}
