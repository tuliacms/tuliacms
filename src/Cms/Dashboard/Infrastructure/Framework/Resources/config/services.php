<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Dashboard\Infrastructure\Framework\Twig\Extension\DashboardExtension;
use Tulia\Cms\Dashboard\Tiles\Manager;
use Tulia\Cms\Dashboard\Tiles\ManagerInterface;
use Tulia\Cms\Dashboard\Widgets\Registry;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

$builder->setDefinition(Registry::class, Registry::class, [
    'arguments' => [
        tagged('dashboard.widget'),
    ],
]);

$builder->setDefinition(DashboardExtension::class, DashboardExtension::class, [
    'arguments' => [
        service(Registry::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(ManagerInterface::class, Manager::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
    ],
]);
