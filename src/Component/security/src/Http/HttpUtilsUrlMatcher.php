<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HttpUtilsUrlMatcher implements RequestMatcherInterface
{
    private RouterInterface $router;
    private RequestContext $requestContext;

    public function __construct(RouterInterface $router, RequestContext $requestContext)
    {
        $this->router = $router;
        $this->requestContext = $requestContext;
    }

    public function matchRequest(Request $request): array
    {
        return $this->router->match($request->attributes->get('_content_path'));
    }

    public function getContext(): RequestContext
    {
        return $this->requestContext;
    }
}
