<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Parser;

use Tulia\Component\Routing\Route;

/**
 * @author Adam Banaszkiewicz
 */
class RouteParser implements RouteParserInterface
{
    public function parse(Route $route): Route
    {
        $parts = [
            'required' => [],
            'optional' => [],
        ];

        if (strpos($route->getPath(), '/{?') !== false && preg_match_all('#/{\?([a-z0-9_]+)}#i', $route->getPath(), $matches))
        {
            $parts['optional'][$matches[1][0]] = [
                'name' => $matches[1][0],
                'replacement' => $matches[0][0],
            ];
        }

        if (strpos($route->getPath(), '{') !== false && preg_match_all('#{([a-z0-9_]+)}#i', $route->getPath(), $matches)) {
            foreach ($matches[0] as $key => $val) {
                $parts['required'][$matches[1][$key]] = [
                    'name' => $matches[1][$key],
                    'replacement' => $matches[0][$key],
                ];
            }
        }

        $route->setParts('required', $parts['required']);
        $route->setParts('optional', $parts['optional']);

        return $route;
    }
}
