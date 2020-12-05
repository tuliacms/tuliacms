<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Integration\FastRoute;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Tulia\Component\Routing\Exception\RequestNoMatchedException;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;
use Tulia\Component\Routing\Route;
use Tulia\Component\Routing\RouteCollectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Matcher implements MatcherInterface
{
    /**
     * @var RouteCollectionInterface
     */
    protected $collection;

    /**
     * @var Dispatcher[]
     */
    protected $dispatcher = [];

    /**
     * @param RouteCollectionInterface $collection
     */
    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo, RequestContextInterface $context): array
    {
        $group = $context->isBackend() ? 'backend' : '';

        $routeInfo = $this->getDispatcher($group)->dispatch($context->getMethod(), $pathinfo);

        if ($routeInfo[0] === Dispatcher::FOUND) {
            $vars = $routeInfo[2];
            $vars['_controller'] = $routeInfo[1];
            $vars['_route_params'] = [];
            $vars['_route'] = null;

            /** @var Route $route */
            foreach ($this->collection->allByGroup($group) as $route) {
                if ($vars['_controller'] === $route->getController()) {
                    $vars['_route_params'] = $routeInfo[2];
                    $vars['_route'] = $route->getName();
                }
            }

            return $vars;
        }

        throw new RequestNoMatchedException(sprintf('Request %s "%s" not matched by Routing.', $context->getMethod(), $pathinfo));
    }

    /**
     * @return Dispatcher
     */
    private function getDispatcher(string $group): Dispatcher
    {
        if (isset($this->dispatcher[$group])) {
            return $this->dispatcher[$group];
        }

        $routeCollector = new RouteCollector(new Std(), new GroupCountBased());

        $routes = $this->collection->allByGroup($group);

        /** @var Route $route */
        foreach ($routes as $name => $route) {
            $path = $route->getPath();

            if (strpos($path, '/{?') !== false) {
                $path = preg_replace('#/{\?([a-z]+)}#i', '[/{$1}]', $path);
            }
            if (strpos($path, '/{!') !== false) {
                $path = preg_replace('#/{!([a-z]+)}#i', '[/{$1}]', $path);
            }

            $routeCollector->addRoute($route->getMethods(), $path, $route->getController());
        }

        return $this->dispatcher[$group] = new Dispatcher($routeCollector->getData());
    }
}
