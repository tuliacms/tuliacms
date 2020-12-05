<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Integration\FastRoute;

use Tulia\Component\Routing\Exception\MissingRouteParameters;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;
use Tulia\Component\Routing\Route;
use Tulia\Component\Routing\RouteCollectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Generator implements GeneratorInterface
{
    /**
     * @var RouteCollectionInterface
     */
    protected $collection;

    /**
     * @var array
     */
    protected static $cache = [];

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
    public function generate(string $name, array $params, RequestContextInterface $context): string
    {
        $cacheKey = md5(serialize([$name, $params]));

        if (isset(static::$cache[$cacheKey])) {
            return static::$cache[$cacheKey];
        }

        if ($this->collection->has($name) === false) {
            throw new RouteNotFoundException(sprintf('Route "%s" not found.', $name));
        }

        $route = $this->collection->get($name);
        $paramsPrepared = [];

        foreach ($route->getDefaults() as $key => $val) {
            $paramsPrepared[$key] = [
                'value' => $val,
                'type'  => 'default',
            ];
        }

        foreach ($params as $key => $val) {
            $paramsPrepared[$key] = [
                'value' => $val,
                'type'  => 'wanted',
            ];
        }

        unset($paramsPrepared['_locale_prefix']);
        unset($paramsPrepared['_locale']);

        list($path, $paramsLeft) = $this->interpolate($route, $paramsPrepared);

        $path = "{$params['_locale_prefix']}$path";

        if ($route->getGroup() === 'backend') {
            $path = "{$context->getWebsite()->getBackendPrefix()}$path";
        }

        //$path = $context->getBasePath() . $path;

        /**
         * Remove special parameters from left params array, to prevent add
         * them info query string to generated path.
         */
        foreach ($paramsPrepared as $key => $item) {
            if ($item['type'] === 'special') {
                unset($paramsLeft[$key]);
            }
        }

        // Remove empty params.
        $paramsLeft = array_filter($paramsLeft, function ($item) {
            return $item['value'] !== null && $item['type'] === 'wanted';
        });
        $paramsLeft = array_map(function ($item) {
            return $item['value'];
        }, $paramsLeft);

        if ($paramsLeft !== []) {
            $path .= '?' . http_build_query($paramsLeft);
        }

        return static::$cache[$cacheKey] = $path;
    }

    /**
     * @param Route $route
     * @param array $params
     *
     * @return array
     *
     * @throws MissingRouteParameters
     */
    private function interpolate(Route $route, array $params): array
    {
        $source = $route->getPath();
        $paramsLeft = $params;
        $paramsRequired = $route->getParts('required');
        $paramsOptional = $route->getParts('optional');

        foreach ($paramsRequired as $name => $param) {
            if (isset($params[$name]) === false) {
                continue;
            }

            $output = str_replace("{{$name}}", $params[$name]['value'], $source);

            unset($paramsLeft[$name], $paramsRequired[$name]);

            $source = $output;
        }

        if ($paramsRequired !== []) {
            $paramsPrepared = array_keys($paramsRequired);
            throw new MissingRouteParameters(sprintf('Missing parameters [%s], of route "%s".', implode($paramsPrepared), $route->getName()));
        }

        foreach ($paramsOptional as $name => $param) {
            if (isset($params[$name]) === false) {
                $output = str_replace($param['replacement'], '', $source);
            } else {
                $output = str_replace("{?{$name}}", $params[$name]['value'], $source);
            }

            unset($paramsLeft[$name], $paramsOptional[$name]);

            $source = $output;
        }

        return [$source, $paramsLeft];
    }
}
