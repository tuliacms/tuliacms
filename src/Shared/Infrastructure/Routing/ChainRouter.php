<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ChainRouter implements ChainRouterInterface
{
    /**
     * @var RouterInterface[][]
     */
    private array $routers = [];

    /**
     * @var RouterInterface[]
     */
    private array $sortedRouters = [];
    private ?RequestContext $context = null;
    private RouteCollection $routeCollection;
    protected ?LoggerInterface $logger = null;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->routeCollection = new RouteCollection();
    }

    /**
     * @return RequestContext
     */
    public function getContext()
    {
        if (!$this->context) {
            $this->context = new RequestContext();
        }

        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function add(RouterInterface $router, int $priority = 0): void
    {
        if (empty($this->routers[$priority])) {
            $this->routers[$priority] = [];
        }

        $this->routers[$priority][] = $router;
        $this->sortedRouters = [];
    }

    public function all(): array
    {
        if (0 === count($this->sortedRouters)) {
            $this->sortedRouters = $this->sortRouters();

            if (null !== $this->context) {
                foreach ($this->sortedRouters as $router) {
                    if ($router instanceof RequestContextAwareInterface) {
                        $router->setContext($this->context);
                    }
                }
            }
        }

        return $this->sortedRouters;
    }

    public function setContext(RequestContext $context): void
    {
        foreach ($this->all() as $router) {
            if ($router instanceof RequestContextAwareInterface) {
                $router->setContext($context);
            }
        }

        $this->context = $context;
    }

    public function getRouteCollection()
    {
        // TODO: Implement getRouteCollection() method.
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH)
    {
        // TODO: Implement generate() method.
    }

    public function match(string $pathinfo): array
    {
        return $this->doMatch($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        return $this->doMatch($request->getPathInfo(), $request);
    }

    protected function doMatch(string $pathinfo, Request $request = null): array
    {
        dump($pathinfo);exit;
    }

    protected function sortRouters(): array
    {
        if ($this->routers === []) {
            return [];
        }

        krsort($this->routers);

        return array_merge(...$this->routers);
    }
}
