<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Requtize\Assetter\AssetterInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\VoidThemeLoader;
use Tulia\Component\Theme\Assetter\ThemeConfigurationAssetsLoader as BaseLoader;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Manager;
use Tulia\Component\Theme\Resolver\ConfigurationResolver;
use Tulia\Component\Theme\Resolver\ResolverAggregate;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\Storage\ArrayStorage;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\TwigBridge\Extension\ThemeExtension;
use Tulia\Component\Theme\TwigBridge\Loader\NamespaceLoader;
use Tulia\Framework\Kernel\Event\BootstrapEvent;
use Tulia\Framework\Kernel\Event\ViewEvent;
use Tulia\Framework\Module\DependenciesLoader as ModuleDependenciesLoader;
use Tulia\Framework\Theme\DependenciesLoader as ThemeDependenciesLoader;
use Tulia\Framework\Theme\Assetter\ThemeConfigurationAssetsLoader;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(ManagerInterface::class, Manager::class, [
    'arguments' => [
        service(StorageInterface::class),
        service(ResolverAggregateInterface::class),
        service(ThemeLoaderInterface::class),
    ],
]);

$builder->setDefinition(StorageInterface::class, ArrayStorage::class);

/*$builder->setDefinition(ResolverAggregateInterface::class, ResolverAggregate::class, [
    'arguments' => [
        tagged('theme.resolver'),
    ],
]);*/

$builder->setDefinition(ThemeLoaderInterface::class, VoidThemeLoader::class);

$builder->setDefinition(BaseLoader::class, BaseLoader::class, [
    'arguments' => [
        service(ManagerInterface::class),
        service(AssetterInterface::class),
    ],
]);

$builder->setDefinition(ConfigurationResolver::class, ConfigurationResolver::class, [
    'arguments' => [
        service(ManagerInterface::class),
        service(DetectorInterface::class),
    ],
    'tags' => [ tag('theme.resolver') ],
]);

$builder->setDefinition(ThemeConfigurationAssetsLoader::class, ThemeConfigurationAssetsLoader::class, [
    'arguments' => [
        service(BaseLoader::class),
    ],
    'tags' => [
        tag_event_listener(ViewEvent::class, 1000),
    ],
]);

$builder->setDefinition(ThemeDependenciesLoader::class, ThemeDependenciesLoader::class, [
    'arguments' => [
        service(ContainerInterface::class),
        service(ManagerInterface::class),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 8000),
    ],
]);

$builder->setDefinition(ModuleDependenciesLoader::class, ModuleDependenciesLoader::class, [
    'arguments' => [
        service(ContainerInterface::class),
        parameter('modules.enabled.list', true),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 8000),
    ],
]);

/*$builder->setDefinition(ThemeExtension::class, ThemeExtension::class, [
    'arguments' => [
        service(ManagerInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);*/

/*$builder->setDefinition(NamespaceLoader::class, NamespaceLoader::class, [
    'arguments' => [
        service(ManagerInterface::class),
    ],
    'tags' => [ tag('twig.loader') ],
]);*/


/*$builder->mergeParameter('twig.loader.array.templates', [
    'theme' => "{% extends [ '@theme/layout.tpl', '@parent/layout.tpl' ] %}",
]);*/
