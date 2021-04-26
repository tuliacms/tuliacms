<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
interface ConfiguratorInterface
{
    /**
     * @param ConfigurationInterface $configuration
     */
    public function configure(ConfigurationInterface $configuration): void;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function configureCustomizer(ConfigurationInterface $configuration): void;
}
