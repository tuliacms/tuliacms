<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Factory;

use Tulia\Cms\SearchAnything\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface EngineFactoryInterface
{
    public function providerEngine(string $provider): EngineInterface;
}
