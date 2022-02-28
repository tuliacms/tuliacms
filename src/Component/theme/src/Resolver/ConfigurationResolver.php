<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\Configuration;
use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Configuration\ConfigurationRegistry;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationResolver implements ResolverInterface
{
    private ManagerInterface $manager;
    private DetectorInterface $detector;
    private ConfigurationRegistry $configurationRegistry;

    public function __construct(
        ManagerInterface $manager,
        DetectorInterface $detector,
        ConfigurationRegistry $configurationRegistry
    ) {
        $this->manager = $manager;
        $this->detector = $detector;
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ThemeInterface $theme): void
    {
        if ($theme->hasConfig()) {
            return;
        }

        $configuration = new Configuration();

        if ($theme->getParent()) {
            $parent = $this->manager->getStorage()->get($theme->getParent());

            $this->configure($configuration, $parent);
        }

        $this->configure($configuration, $theme);

        $theme->setConfig($configuration);
    }

    private function configure(ConfigurationInterface $configuration, ThemeInterface $theme): void
    {
        $configuration->merge($this->configurationRegistry->get($theme->getName(), 'base'));

        if ($this->detector->isCustomizerMode()) {
            $configuration->merge($this->configurationRegistry->get($theme->getName(), 'customizer'));
        }
    }
}
