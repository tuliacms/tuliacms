<?php

namespace Tulia\Component\Hooking\Tests\Unit;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown()
    {
        if($container = \Mockery::getContainer())
        {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        \Mockery::close();
    }
}
