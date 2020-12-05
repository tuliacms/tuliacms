<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\Parser\RouteParser;
use Tulia\Component\Routing\Parser\RouteParserInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var RouteParserInterface
     */
    protected $parser;

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function allByGroup(string $group = ''): array
    {
        $routes = [];

        foreach ($this->routes as $name => $route) {
            if ($route->getGroup() === $group) {
                $routes[$name] = $route;
            }
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): Route
    {
        if (isset($this->routes[$name]) === false) {
            throw new RouteNotFoundException(sprintf('Route %s not found in collection.', $name));
        }

        return $this->parseRoute($this->routes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $name, string $path, array $options = []): void
    {
        $methods = $options['methods'] ?? 'GET';
        $methods = \is_array($methods) ? $methods : [ $methods ];

        $route = new Route($name, $path, $options['controller'], $methods);
        $route->setDefaults($options['defaults'] ?? []);
        $route->setSuffix($options['suffix'] ?? '');
        $route->setGroup($options['group'] ?? '');

        $this->routes[$name] = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        unset($this->routes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function addNamePrefix(string $prefix): void
    {
        $newRoutes = [];

        foreach ($this->routes as $name => $route) {
            $newName = $prefix.$name;

            $newRoutes[$newName] = $route;
            $newRoutes[$newName]->setName($newName);
        }

        $this->routes = $newRoutes;
    }

    /**
     * {@inheritdoc}
     */
    public function addPathPrefix(string $prefix): void
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name]->setPath($prefix . $this->routes[$name]->getPath());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(string $group): void
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name]->setGroup($group);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(RouteCollectionInterface $collection): void
    {
        foreach ($collection->all() as $name => $route) {
            $this->routes[$name] = $route;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function group(string $name, callable $callable): void
    {
        $collection = new self();

        $callable($collection);

        $collection->addNamePrefix($name . '.');
        $collection->setGroup($name);

        $this->merge($collection);
    }

    protected function parseRoute(Route $route): Route
    {
        if (!$this->parser) {
            $this->parser = new RouteParser();
        }

        return $this->parser->parse($route);
    }
}
