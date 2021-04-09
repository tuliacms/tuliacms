<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Routing;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ChainRouterInterface extends RouterInterface, RequestMatcherInterface
{
    public function add(RouterInterface $router, int $priority = 0): void;

    /**
     * @return RouterInterface[]
     */
    public function all();
}
