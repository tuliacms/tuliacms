<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Plugin;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return iterable
     */
    public function getPlugins(): iterable;

    /**
     * @param $method
     * @param mixed ...$args
     */
    public function callPlugins($method, ...$args): void;
}
