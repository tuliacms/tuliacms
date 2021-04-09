<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyRouterDecorator implements RouterInterface, RequestMatcherInterface
{
    private RouterInterface $symfonyRouter;

    public function __construct(RouterInterface $symfonyRouter, ChainRouterInterface $chainRouter)
    {
        $this->symfonyRouter = $symfonyRouter;
    }

    public function setContext(RequestContext $context): void
    {
        $this->symfonyRouter->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->symfonyRouter->getContext();
    }

    public function matchRequest(Request $request): array
    {
        return $this->symfonyRouter->matchRequest($request);
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->symfonyRouter->getRouteCollection();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->symfonyRouter->generate($name, $parameters, $referenceType);
    }

    public function match(string $pathinfo): array
    {
        return $this->symfonyRouter->match($pathinfo);
    }
}
