<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Plugin;

use Tulia\Component\Theme\Plugin\RegistryInterface as BaseRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface extends BaseRegistryInterface
{
    /**
     * @param PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin): void;
}
