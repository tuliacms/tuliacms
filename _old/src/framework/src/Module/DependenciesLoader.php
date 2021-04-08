<?php

declare(strict_types=1);

namespace Tulia\Framework\Module;

use Tulia\Component\DependencyInjection\ContainerInterface;
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
     * @var array
     */
    protected $enabledModules;

    /**
     * @param ContainerInterface $container
     * @param array $enabledModules
     */
    public function __construct(ContainerInterface $container, ?array $enabledModules = null)
    {
        $this->container      = $container;
        $this->enabledModules = $enabledModules ?? [];
    }

    public function handle(BootstrapEvent $event): void
    {
        foreach ($this->enabledModules as $module) {
            $this->container->mergeGroup('module.' . $module);
        }
    }
}
