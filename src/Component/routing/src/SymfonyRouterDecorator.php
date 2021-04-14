<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
    private ?LoggerInterface $logger;

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

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        foreach ($this->routers() as $router) {
            $path = $router->generate($name, $parameters, $referenceType);

            if ($path !== null && $path !== '') {
                return $this->appendWebsitePrefixes($name, $path, $parameters, $referenceType);
            }
        }

        return '';
    }

    public function match(string $pathinfo): array
    {
        return $this->doMatch($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        return $this->doMatch($request->getPathInfo(), $request);
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
                $this->log('Router ' . get_class($router) . ' was not able to match, message "' . $e->getMessage() . '"');
            } catch (MethodNotAllowedException $e) {
                $this->log('Router ' . get_class($router) . ' throws MethodNotAllowedException with message "' . $e->getMessage() . '"');
                $methodNotAllowedException = $e;
            }
        }

        $info = $request
            ? "this request\n$request"
            : "url '$pathinfo'";

        throw $methodNotAllowedException ?: new ResourceNotFoundException("None of the routers in the chain matched $info");
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

    private function appendWebsitePrefixes(string $name, string $uri, array $parameters, int $referenceType): string
    {
        /** @var array $parts */
        $parts = parse_url($uri);

        if (! isset($parts['path'])) {
            $parts['path'] = '/';
        }

        if (strpos('backend_', $name) === 0) {
            $parts['path'] = $this->currentWebsite->getPathPrefix() . $this->currentWebsite->getBackendAddress() . $this->currentWebsite->getLocalePrefix() . $parts['path'];
        } elseif (strpos('api_', $name) === 0) {

        } else {
            $parts['path'] = $this->currentWebsite->getPathPrefix() . $this->currentWebsite->getLocalePrefix() . $parts['path'];
        }

        return
            (isset($parts['scheme']) ? $parts['scheme'] . '://' : '') .
            ($parts['host'] ?? '') .
            ($parts['path'] ?? '') .
            (isset($parts['query']) ? '?' . $parts['query'] : '')
        ;
    }
}
