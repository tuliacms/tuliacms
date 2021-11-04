<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyRouterDecorator implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    private RouterInterface $symfonyRouter;

    private ChainRouterInterface $chainRouter;

    private CurrentWebsiteInterface $currentWebsite;

    private ?LoggerInterface $logger = null;

    private ?WebsitePrefixesResolver $websitePrefixesResolver = null;

    public function __construct(
        RouterInterface $symfonyRouter,
        ChainRouterInterface $chainRouter,
        CurrentWebsiteInterface $currentWebsite,
        LoggerInterface $logger = null
    ) {
        $this->symfonyRouter = $symfonyRouter;
        $this->chainRouter = $chainRouter;
        $this->currentWebsite = $currentWebsite;
        $this->logger = $logger;
    }

    public function setContext(RequestContext $context): void
    {
        foreach ($this->routers() as $router) {
            $router->setContext($context);
        }
    }

    public function getContext(): RequestContext
    {
        return $this->symfonyRouter->getContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        $collection = new RouteCollection();

        foreach ($this->routers() as $router) {
            $collection->addCollection($router->getRouteCollection());
        }

        return $collection;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): ?string
    {
        $originalParameters = $parameters;
        unset($parameters['_locale']);

        foreach ($this->routers() as $router) {
            try {
                $router->setContext($this->getContext());
                $path = $router->generate($name, $parameters, $referenceType);

                if ($path !== null) {
                    return $this->getWebsitePrefixesResolver()->appendWebsitePrefixes(
                        $name,
                        $path,
                        $originalParameters
                    );
                }
            } catch (RouteNotFoundException $e) {
                continue;
            }
        }

        throw new RouteNotFoundException(sprintf('None of the routers in the chain matched route named %s.', $name));
    }

    public function match(string $pathinfo): array
    {
        return $this->doMatch($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        return $this->doMatch($request->attributes->get('_content_path', $request->getPathInfo()), $request);
    }

    public function warmUp(string $cacheDir): array
    {
        $result = [];

        foreach ($this->routers() as $router) {
            if ($router instanceof WarmableInterface) {
                $result[] = $router->warmUp($cacheDir);
            }
        }

        return array_merge(...$result);
    }

    /**
     * @return RouterInterface[]|RequestMatcherInterface[]
     */
    public function routers(): array
    {
        return array_merge([$this->symfonyRouter], $this->chainRouter->all());
    }

    private function doMatch(string $pathinfo, Request $request = null): array
    {
        $methodNotAllowedException = null;
        $requestForMatching = $request;

        foreach ($this->routers() as $router) {
            try {
                if ($router instanceof RequestMatcherInterface) {
                    if (null === $requestForMatching) {
                        $requestForMatching = $this->createRequest($pathinfo);
                    }

                    return $router->matchRequest($requestForMatching);
                }

                return $router->match($pathinfo);
            } catch (ResourceNotFoundException $e) {
                $this->log('Router ' . \get_class($router) . ' was not able to match, message "' . $e->getMessage() . '"');
            } catch (MethodNotAllowedException $e) {
                $this->log('Router ' . \get_class($router) . ' throws MethodNotAllowedException with message "' . $e->getMessage() . '"');
                $methodNotAllowedException = $e;
            }
        }

        $info = $request
            ? "this request\n$request"
            : "url '$pathinfo'";

        throw $methodNotAllowedException ?: new ResourceNotFoundException("None of the routers in the chain matched $info", 0, $e);
    }

    private function createRequest(string $pathinfo): Request
    {
        $context = $this->getContext();
        $uri = $pathinfo;
        $serverData = [];

        if ($context->getBaseUrl()) {
            $uri = $context->getBaseUrl() . $pathinfo;
            $serverData['SCRIPT_FILENAME'] = $context->getBaseUrl();
            $serverData['PHP_SELF'] = $context->getBaseUrl();
        }

        $host = $context->getHost() ?: 'localhost';

        if ('https' === $context->getScheme() && 443 !== $context->getHttpsPort()) {
            $host .= ':' . $context->getHttpsPort();
        } elseif ('http' === $context->getScheme() && 80 !== $context->getHttpPort()) {
            $host .= ':' . $context->getHttpPort();
        }

        $uri = $context->getScheme() . '://' . $host . $uri . '?' . $context->getQueryString();

        return Request::create($uri, $context->getMethod(), $context->getParameters(), [], [], $serverData);
    }

    private function log(string $message): void
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }

    private function getWebsitePrefixesResolver(): WebsitePrefixesResolver
    {
        if ($this->websitePrefixesResolver === null) {
            $this->websitePrefixesResolver = new WebsitePrefixesResolver($this->currentWebsite);
        }

        return $this->websitePrefixesResolver;
    }
}
