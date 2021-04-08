<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractConfigurator implements ConfiguratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(ConfigurationInterface $configuration): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function configureCustomizer(ConfigurationInterface $configuration): void
    {

    }
}
