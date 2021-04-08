<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Exception\RouteNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RouteCollectionInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string|null $group
     *
     * @return array
     */
    public function allByGroup(string $group = ''): array;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     *
     * @return Route
     *
     * @throws RouteNotFoundException
     */
    public function get(string $name): Route;

    /**
     * @param string $name
     */
    public function remove(string $name): void;

    /**
     * @param string $name
     * @param string $path
     * @param array  $options
     */
    public function add(string $name, string $path, array $options = []): void;

    /**
     * @param string $prefix
     */
    public function addNamePrefix(string $prefix): void;

    /**
     * @param string $prefix
     */
    public function addPathPrefix(string $prefix): void;

    /**
     * @param string $group
     */
    public function setGroup(string $group): void;

    /**
     * @param RouteCollectionInterface $collection
     */
    public function merge(RouteCollectionInterface $collection): void;

    /**
     * @param string   $name
     * @param callable $callable
     */
    public function group(string $name, callable $callable): void;
}
