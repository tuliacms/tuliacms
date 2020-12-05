<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Tests\Unit;

use Tulia\Component\Routing\RouteCollection;

/**
 * @author Adam Banaszkiewicz
 */
class RouteCollectionTest extends TestCase
{
    public function testInterface(): void
    {
        $collection = new RouteCollection;

        $this->assertCount(0, $collection->all());
        $this->assertFalse($collection->has('homepage'));
        $this->assertNull($collection->get('homepage'));

        $collection->add('homepage', '/', [
            'controller' => 'MyController',
        ]);

        $this->assertCount(1, $collection->all());
        $this->assertTrue($collection->has('homepage'));
        $this->assertIsArray($collection->get('homepage'));
        $this->assertSame($collection->get('homepage'), [
            'name'       => 'homepage',
            'path'       => '/',
            'defaults'   => [],
            'controller' => 'MyController',
            'methods'    => [ 'GET' ],
        ]);

        $collection->remove('homepage');

        $this->assertCount(0, $collection->all());
        $this->assertFalse($collection->has('homepage'));
        $this->assertNull($collection->get('homepage'));
    }

    public function testMethods(): void
    {
        $collection = new RouteCollection;
        $collection->add('empty', '/');
        $collection->add('string', '/', [ 'methods' => 'GET' ]);
        $collection->add('array', '/', [ 'methods' => [ 'GET', 'POST' ] ]);

        $this->assertSame([ 'GET' ], $collection->get('empty')['methods']);
        $this->assertSame([ 'GET' ], $collection->get('string')['methods']);
        $this->assertSame([ 'GET', 'POST' ], $collection->get('array')['methods']);
    }

    public function testDefaults(): void
    {
        $collection = new RouteCollection;
        $collection->add('empty', '/');
        $collection->add('filled', '/', [ 'defaults' => [ 'some-key' => 'some-value' ] ]);

        $this->assertArrayHasKey('defaults', $collection->get('empty'));
        $this->assertSame([], $collection->get('empty')['defaults']);
        $this->assertSame([ 'some-key' => 'some-value' ], $collection->get('filled')['defaults']);
    }

    public function testOverwrite(): void
    {
        $collection = new RouteCollection;
        $collection->add('name', '/');
        $collection->add('name', '/name', [
            'methods' => 'POST',
        ]);

        $this->assertSame([
            'name'       => 'name',
            'path'       => '/name',
            'defaults'   => [],
            'controller' => null,
            'methods'    => [ 'POST' ],
        ], $collection->get('name'));
    }
}
