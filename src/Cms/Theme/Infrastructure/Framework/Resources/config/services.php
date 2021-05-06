<?php declare(strict_types=1);

use Requtize\Assetter\AssetterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\FrontendToolbar\Application\Helper\HelperInterface;
use Tulia\Cms\Options\Domain\ReadModel\Options;
use Tulia\Cms\Theme\Application\Service\ThemeActivator;
use Tulia\Cms\Theme\Infrastructure\Cms\FrontendToolbar\LinksProvider;
use Tulia\Cms\Theme\Infrastructure\Filemanager\ImageSize\ThemeConfigurationProvider;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator\Activator;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\AssetsLoader;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Changeset;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Storage\DatabaseStorage;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader\ThemeLoader;
use Tulia\Cms\Theme\Infrastructure\Cms\BackendMenu\AppearenceMenuBuilder;
use Tulia\Cms\Theme\Infrastructure\Framework\Twig\Extension\ThemeExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Hooking\HookerInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Component\Theme\Storage\ArrayStorage;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface as ChangesetStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/** @var ContainerBuilderInterface $builder */

/*$builder->setDefinition(ThemeLoaderInterface::class, ThemeLoader::class, [
    'arguments' => [
        service(StorageInterface::class),
        service(Options::class),
    ],
]);*/

$builder->setDefinition(StorageInterface::class, ArrayStorage::class, [
    'arguments' => [
        parameter('kernel.themes'),
    ],
]);

/*$builder->setDefinition(ActivatorInterface::class, Activator::class, [
    'arguments' => [
        service(StorageInterface::class),
        service(Options::class),
    ],
]);*/

$builder->setDefinition(ChangesetStorageInterface::class, DatabaseStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(RequestStack::class),
        service(ChangesetFactoryInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

/*$builder->setDefinition(AssetsLoader::class, AssetsLoader::class, [
    'arguments' => [
        service(AssetterInterface::class),
        service(DetectorInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class),
    ],
]);*/

/*$builder->setDefinition(AppearenceMenuBuilder::class, AppearenceMenuBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
    ],
    'tags' => [ tag('backend_menu.builder') ],
]);*/

/*$builder->setDefinition(ThemeExtension::class, ThemeExtension::class, [
    'arguments' => [
        service(HookerInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);*/

/*$builder->setDefinition(ThemeConfigurationProvider::class, ThemeConfigurationProvider::class, [
    'arguments' => [
        service(ManagerInterface::class),
    ],
    'tags' => [ tag('filemanager.image_size.provider') ],
]);*/

/*$builder->setDefinition(LinksProvider::class, LinksProvider::class, [
    'arguments' => [
        service(HelperInterface::class),
    ],
    'tags' => [ tag('frontend_toolbar.links.provider') ],
]);*/

$builder->setDefinition(ThemeActivator::class, ThemeActivator::class, [
    'arguments' => [
        service(ManagerInterface::class),
        service(ActivatorInterface::class),
        service(EventDispatcherInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);


/*$builder->setParameter('theme.changeset.base_class', Changeset::class);*/

/*$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);*/

/*$builder->mergeParameter('templating.paths', [
    'cms/theme' => dirname(__DIR__) . '/views/frontend',
    'backend/theme' => dirname(__DIR__) . '/views/backend',
]);*/
/*$builder->mergeParameter('twig.loader.array.templates', [
    'backend' => "{% extends '@backend/layout/layout.tpl' %}",
]);*/
