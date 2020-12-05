<?php

declare(strict_types=1);

namespace Tulia\Framework\Theme;

use Tulia\Component\DependencyInjection\ContainerInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Framework\Kernel\Event\BootstrapEvent;

/**
 * @author Adam Banaszkiewicz
 */
class DependenciesLoader
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ContainerInterface $container
     * @param ManagerInterface $manager
     */
    public function __construct(ContainerInterface $container, ManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager   = $manager;
    }

    public function handle(BootstrapEvent $event): void
    {
        /** @var ThemeInterface $theme */
        $theme = $this->manager->getLoader()->load();

        if ($theme->getParent()) {
            $this->container->mergeGroup('theme.' . $theme->getParent());
            $this->container->mergeParameter('templating.paths', [
                'parent' => $this->container->getParameter('templating.paths')["_theme_views/{$theme->getParent()}"] ?? null,
            ]);
        }

        $name = $theme->getName();
        $this->container->mergeGroup('theme.' . $name);
        $this->container->mergeParameter('templating.paths', [
            'theme' => $this->container->getParameter('templating.paths')["_theme_views/{$name}"] ?? null,
        ]);
    }
}
