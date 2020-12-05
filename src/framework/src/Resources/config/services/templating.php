<?php declare(strict_types=1);

use Requtize\Assetter\Assetter;
use Requtize\Assetter\AssetterInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\Platform\Version;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Templating\Assetter\Factory;
use Tulia\Component\Templating\Config;
use Tulia\Component\Templating\Engine;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\EventListener\AssetsDocumentLoader;
use Tulia\Component\Templating\EventListener\ResponseViewRenderer;
use Tulia\Component\Templating\Twig\Extension\AssetterExtention;
use Tulia\Component\Templating\Twig\Extension\BaseTwigExtension;
use Tulia\Component\Templating\ViewFilter\DelegatingFilter;
use Tulia\Component\Templating\ViewFilter\FilterInterface;
use Tulia\Component\Templating\ViewFilter\ViewNamespaceOverwriteFilter;
use Tulia\Framework\Kernel\Event\ViewEvent;
use Tulia\Framework\Twig\Extension\AppExtension;
use Twig\Environment;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(Config::class, Config::class, [
    'factory' => function ($config) {
        return new Config([
            'namespace_overwrite' => $config,
        ]);
    },
    'arguments' => [
        parameter('templating.namespace_overwrite'),
    ],
]);

$builder->setDefinition(EngineInterface::class, Engine::class, [
    'arguments' => [
        service(Environment::class),
    ],
]);

$builder->setDefinition(FilterInterface::class, DelegatingFilter::class, [
    'arguments' => [
        tagged('templating.view_filter'),
    ],
]);

$builder->setDefinition(ResponseViewRenderer::class, ResponseViewRenderer::class, [
    'arguments' => [
        service(EngineInterface::class),
    ],
    'tags' => [
        tag_event_listener(ViewEvent::class, 500, 'onKernelView'),
    ],
]);

$builder->setDefinition(AssetterInterface::class, Assetter::class, [
    'factory' => [ Factory::class, 'factory' ],
    'arguments' => [
        parameter('assets'),
        [
            'global_revision' => Version::VERSION,
        ],
    ],
]);

$builder->setDefinition(AssetsDocumentLoader::class, AssetsDocumentLoader::class, [
    'arguments' => [
        service(AssetterInterface::class),
    ],
    'tags' => [
        tag_action('theme-head', 'loadThemeHead', 500),
        tag_action('theme-body', 'loadThemeBody', 500),
    ],
]);

$builder->setDefinition(ViewNamespaceOverwriteFilter::class, ViewNamespaceOverwriteFilter::class, [
    'arguments' => [
        service(Config::class),
    ],
    'tags' => [ tag('templating.view_filter') ],
]);


/**
 * Twig Extensions.
 */
$builder->setDefinition(BaseTwigExtension::class, BaseTwigExtension::class, [
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(AssetterExtention::class, AssetterExtention::class, [
    'arguments' => [
        service(AssetterInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(AppExtension::class, AppExtension::class, [
    'arguments' => [
        service( RequestStack::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(FormExtension::class, FormExtension::class, [
    'tags' => [ tag('twig.extension') ],
]);
