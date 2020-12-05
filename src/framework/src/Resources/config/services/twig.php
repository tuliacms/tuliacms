<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;
use Tulia\Component\Templating\Twig\Loader\FilesystemLoaderFactory;
use Tulia\Component\Templating\Twig\Loader\LazyArrayLoader;
use Tulia\Component\Templating\ViewFilter\FilterInterface;
use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Profiler\Profile;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\RuntimeLoader\ContainerRuntimeLoader;

/** @var ContainerBuilderInterface $builder */

try {
    $debug = $builder->getParameter('kernel.debug');
} catch (\Exception $e) {
    $debug = false;
}

$builder->setDefinition(Environment::class, Environment::class, [
    'factory' => function (LoaderInterface $loader, RuntimeLoaderInterface $runtimeLoader, Profile $profile, bool $debug) {
        $twig = new Environment($loader, [
            'debug' => $debug,
            'strict_variables' => $debug,
        ]);
        $twig->addRuntimeLoader($runtimeLoader);

        if ($debug) {
            $twig->addExtension(new ProfilerExtension($profile));
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    },
    'arguments' => [
        service(LoaderInterface::class),
        service(RuntimeLoaderInterface::class),
        service(Profile::class),
        parameter('kernel.debug'),
    ],
    'pass_tagged' => [
        'twig.extension' => 'addExtension',
    ],
]);

$builder->setDefinition(RuntimeLoaderInterface::class, ContainerRuntimeLoader::class, [
    'arguments' => [
        service(ContainerInterface::class),
    ],
]);

$builder->setDefinition(LoaderInterface::class, ChainLoader::class, [
    'pass_tagged' => [
        'twig.loader' => 'addLoader',
    ],
]);

$builder->setDefinition(FilesystemLoader::class, FilesystemLoader::class, [
    'factory' => [ FilesystemLoaderFactory::class, 'factory' ],
    'arguments' => [
        parameter('twig.loader.filesystem.paths'),
    ],
    'tags' => [ tag('twig.loader') ],
]);

$builder->setDefinition(AdvancedFilesystemLoader::class, AdvancedFilesystemLoader::class, [
    'factory' => function ($filter, $paths) {
        return new AdvancedFilesystemLoader($filter, array_combine(
            array_map(static function ($key) {
                return "@{$key}";
            }, array_keys($paths)),
            array_values($paths)
        ));
    },
    'arguments' => [
        service(FilterInterface::class),
        parameter('templating.paths'),
    ],
    'tags' => [ tag('twig.loader') ],
]);

$builder->setDefinition(LazyArrayLoader::class, LazyArrayLoader::class, [
    'arguments' => [
        parameter('twig.loader.array.templates'),
    ],
    'tags' => [ tag('twig.loader') ],
]);

$builder->setDefinition(StringLoaderExtension::class, StringLoaderExtension::class, [
    'tags' => [ tag('twig.extension') ],
]);


/**
 * Twig profiler.
 */
$builder->setDefinition(Profile::class, Profile::class);

$builder->setDefinition(TwigDataCollector::class, TwigDataCollector::class, [
    'arguments' => [
        service(Profile::class),
        service(Environment::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

/**
 * Default parameters.
 */
$builder->mergeParameter('twig.loader.array.templates', []);
$builder->mergeParameter('twig.loader.filesystem.paths', [
    $builder->getParameter('kernel.system_dir') . '/vendor/symfony/twig-bridge/Resources/views/Form',
    'root' => $builder->getParameter('kernel.project_dir'),
]);
$builder->mergeParameter('twig.layout.themes', [
    'bootstrap_4_layout.html.twig',
]);
