<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Plugin;

use Tulia\Component\Theme\Plugin\Registry as BaseRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class Registry extends BaseRegistry implements RegistryInterface
{
    /**
     * {@inheritdoc}
     */
    public function addPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }
}
