<?php

declare(strict_types=1);

namespace Tulia\Component\Config;

/**
 * @author Adam Banaszkiewicz
 */
class Configuration implements ConfigurationInterface
{
    private array $configuration = [];

    public function set(string $key, $data): void
    {

    }

    public function merge(array $configuration): void
    {

    }
}
