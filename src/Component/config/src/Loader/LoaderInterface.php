<?php

declare(strict_types=1);

namespace Tulia\Component\Config\Loader;

use Tulia\Component\Config\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface LoaderInterface
{
    public function supports($resource): bool;

    public function load($resource, ConfigurationInterface $configuration): void;
}
