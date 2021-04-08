<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @author Adam Banaszkiewicz
 */
class TestCase extends BaseTestCase
{
    public function tearDown(): void
    {
        \Mockery::close();
    }
}
