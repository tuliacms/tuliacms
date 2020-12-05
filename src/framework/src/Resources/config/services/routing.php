<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\EventListener\BackendResolver;
use Tulia\Component\Routing\EventListener\LocaleResolver;
use Tulia\Component\Routing\EventListener\RequestMatcher;
use Tulia\Component\Routing\EventListener\WebsiteResolver;
use Tulia\Component\Routing\Generator\DelegatingGenerator;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\Integration\FastRoute\Matcher;
use Tulia\Component\Routing\Integration\FastRoute\Generator;
use Tulia\Component\Routing\Matcher\DelegatingMatcher;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Component\Routing\Request\DynamicRequestContext;
use Tulia\Component\Routing\Request\RequestContextInterface;
use Tulia\Component\Routing\RouteCollection;
use Tulia\Component\Routing\RouteCollectionInterface;
use Tulia\Component\Routing\Router;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Twig\RoutingExtension;
use Tulia\Component\Routing\Website\CurrentWebsite;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\Locale\Storage\ArrayStorage;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;
use Tulia\Component\Routing\Website\Registry;
use Tulia\Component\Routing\Website\RegistryInterface;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Framework\Kernel\Event\BootstrapEvent;
use Tulia\Framework\Kernel\Event\RequestEvent;

/** @var ContainerBuilderInterface $builder */
$builder->setDefinition(RouteCollectionInterface::class, RouteCollection::class);

$builder->setDefinition(MatcherInterface::class, DelegatingMatcher::class, [
    'arguments' => [
        tagged('router.matcher'),
    ],
]);

$builder->setDefinition(Matcher::class, Matcher::class, [
    'arguments' => [
        service(RouteCollectionInterface::class),
    ],
    'tags' => [ tag('router.matcher') ],
]);

$builder->setDefinition(Generator::class, Generator::class, [
    'arguments' => [
        service(RouteCollectionInterface::class),
    ],
    'tags' => [ tag('router.generator') ],
]);

$builder->setDefinition(GeneratorInterface::class, DelegatingGenerator::class, [
    'arguments' => [
        tagged('router.generator'),
    ],
]);

$builder->setDefinition(RouterInterface::class, Router::class, [
    'arguments' => [
        service(CurrentWebsiteInterface::class),
        service(MatcherInterface::class),
        service(GeneratorInterface::class),
        service(RequestContextInterface::class),
    ],
]);

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'factory' => function () {
        $websites = new Registry();

        $localeEnUs = new Locale('en_US', $_SERVER['HTTP_HOST'], null, null, SslModeEnum::ALLOWED_BOTH);
        $localePlPl = new Locale('pl_PL', $_SERVER['HTTP_HOST'], null, null, SslModeEnum::ALLOWED_BOTH);

        $websites->add(new Website(
            'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
            [$localeEnUs, $localePlPl],
            $localeEnUs,
            '/administrator',
            'Default website'
        ));

        return $websites;
    },
]);

$builder->setDefinition(StorageInterface::class, ArrayStorage::class);

$builder->setDefinition(CurrentWebsiteInterface::class, CurrentWebsite::class);

$builder->setDefinition(RequestContextInterface::class, DynamicRequestContext::class, [
    'arguments' => [
        service(RequestStack::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(RequestMatcher::class, RequestMatcher::class, [
    'arguments' => [
        service(RouterInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 200, 'onRequest'),
    ]
]);

$builder->setDefinition(BackendResolver::class, BackendResolver::class, [
    'arguments' => [
        service(CurrentWebsiteInterface::class),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 9800),
    ]
]);

$builder->setDefinition(LocaleResolver::class, LocaleResolver::class, [
    'arguments' => [
        service(CurrentWebsiteInterface::class),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 9500),
    ]
]);

$builder->setDefinition(WebsiteResolver::class, WebsiteResolver::class, [
    'arguments' => [
        service(RegistryInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
    'tags' => [
        tag_event_listener(BootstrapEvent::class, 9900),
    ]
]);

$builder->setDefinition(RoutingExtension::class, RoutingExtension::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition('dynamic_routing_collection', 'dynamic_routing_collection', [
    'factory' => function (RouteCollectionInterface $collection, array $dirs) {
        return function () use ($collection, $dirs) {
            foreach ($dirs as $dir) {
                $collection->group('backend', function (RouteCollectionInterface $collection) use ($dir) {
                    is_file($dir . '/routes-backend.php') && include $dir . '/routes-backend.php';
                });
                is_file($dir . '/routes-frontend.php') && include $dir . '/routes-frontend.php';
            }
        };
    },
    'arguments' => [
        service(RouteCollectionInterface::class),
        parameter('routing.directory_list'),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 1000),
    ],
]);


/**
 * Store list of directories with routing configuration files.
 */
$builder->mergeParameter('routing.directory_list', []);
