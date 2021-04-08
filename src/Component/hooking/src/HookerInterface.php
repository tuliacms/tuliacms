<?php

declare(strict_types=1);

namespace Tulia\Component\Hooking;

/**
 * @author Adam Banaszkiewicz
 */
interface HookerInterface
{
    public function registerAction(string $action, callable $callable, int $priority = 0): void;

    public function registerFilter(string $filter, callable $callable, int $priority = 0): void;

    public function doAction(string $action, array $arguments = []);

    public function doFilter(string $filter, $content = null, array $arguments = []);
}
