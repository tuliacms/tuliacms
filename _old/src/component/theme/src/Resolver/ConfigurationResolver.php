<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Configuration\ConfiguratorInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Configuration\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationResolver implements ResolverInterface
{
    protected ManagerInterface $manager;
    protected DetectorInterface $detector;

    public function __construct(ManagerInterface $manager, DetectorInterface $detector)
    {
        $this->manager = $manager;
        $this->detector = $detector;
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

    /**
     * @param ConfigurationInterface $configuration
     * @param ThemeInterface $theme
     */
    private function configure(ConfigurationInterface $configuration, ThemeInterface $theme): void
    {
        $classname = substr(\get_class($theme), 0, -5) . 'Configurator';

        if (class_exists($classname)) {
            /** @var ConfiguratorInterface $configurator */
            $configurator = new $classname();
            $configurator->configure($configuration);

            if ($this->detector->isCustomizerMode()) {
                $configurator->configureCustomizer($configuration);
            }
        }
    }
}
