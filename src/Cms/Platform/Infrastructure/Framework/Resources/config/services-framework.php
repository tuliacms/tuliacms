<?php declare(strict_types=1);

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\Platform\Infrastructure\Framework\EventListener\ExceptionListener\RequestCsrfTokenExceptionListener;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\EventListener\RouteCollector;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Datatable\Plugin\PluginsRegistry;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Image\DriverFactory;
use Tulia\Component\Image\ImageManager;
use Tulia\Component\Image\ImageManagerInterface;
use Tulia\Component\Routing\RouteCollectionInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Framework\Database\Command\GenerateDatabase;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Framework\Database\VoidConnection;
use Tulia\Framework\Kernel\Event\BootstrapEvent;
use Tulia\Framework\Kernel\Event\ExceptionEvent;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(RequestCsrfTokenExceptionListener::class, RequestCsrfTokenExceptionListener::class, [
    'arguments' => [
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [
        tag_event_listener(ExceptionEvent::class),
    ],
]);

/*$builder->setDefinition(RouteCollector::class, RouteCollector::class, [
    'arguments' => [
        service(RouteCollectionInterface::class),
        parameter('kernel.project_dir'),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 1000, 'collect'),
    ]
]);*/

/*$builder->setDefinition(ImageManagerInterface::class, ImageManager::class, [
    'factory' => DriverFactory::class . '::create',
]);*/

$builder->setDefinition(FrontendRouteSuffixResolver::class, FrontendRouteSuffixResolver::class, [
    'arguments' => [
        service(Options::class),
    ],
]);

$builder->setDefinition(DatatableFactory::class, DatatableFactory::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(PluginsRegistry::class),
    ],
]);

$builder->setDefinition(PluginsRegistry::class, PluginsRegistry::class, [
    'arguments' => [
        tagged('datatable.plugin'),
    ],
]);

$builder->setDefinition(GenerateDatabase::class, GenerateDatabase::class, [
    'tags' => [
        tag_console_command('generate:database')
    ],
]);

if (!tulia_installed() && !tulia_configured()) {
    $builder->setDefinition(ConnectionInterface::class, VoidConnection::class, [
        'factory' => 'Doctrine\DBAL\DriverManager::getConnection',
        'arguments' => [
            [
                'driver'       => 'pdo_mysql',
                'wrapperClass' => VoidConnection::class,
            ],
        ],
    ]);
}



/*$builder->setParameter('security.authentication.login_path', 'backend.login');*/

/*$builder->mergeParameter('templating.namespace_overwrite',  [
    '@parent/' => '@theme/',
    '@cms/'    => '@parent/overwrite/cms/',
    '@module/' => '@parent/overwrite/module/',
    '@widget/' => '@parent/overwrite/widget/',
]);*/

/*$builder->mergeParameter('twig.loader.filesystem.paths', [
    'backend' => dirname(__DIR__) . '/views/backend',
]);*/

/*$builder->mergeParameter('templating.paths', [
    '_theme_views/DefaultTheme' => dirname(__DIR__, 3) . "/DefaultTheme/Resources/views",
]);*/
