<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Parser;

use Tulia\Component\Routing\Route;

/**
 * @author Adam Banaszkiewicz
 */
interface RouteParserInterface
{
    public function parse(Route $route): Route;
}
