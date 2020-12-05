<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\Translation\DataCollector\TranslationDataCollector;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\RegistryInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Framework\Translation\Factory;
use Tulia\Framework\Translation\LocaleResolver;
use Tulia\Framework\Twig\Extension\TranslatorExtension;
use Tulia\Framework\Twig\Extension\WebsiteExtension;

$builder->setDefinition(Translator::class, Translator::class, [
    'factory' => [ Factory::class, 'create' ],
    'arguments' => [
        service(CurrentWebsiteInterface::class),
        parameter('translation.directory_list'),
        parameter('kernel.debug'),
        parameter('kernel.cache_dir'),
    ],
]);

$builder->setAlias(TranslatorInterface::class, Translator::class);

$builder->setDefinition(TranslatorExtension::class, TranslatorExtension::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(WebsiteExtension::class, WebsiteExtension::class, [
    'arguments' => [
        service(CurrentWebsiteInterface::class),
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(LocaleResolver::class, LocaleResolver::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 450),
    ],
]);

$builder->setDefinition(DataCollectorTranslator::class, DataCollectorTranslator::class, [
    'arguments' => [
        service(Translator::class),
    ],
]);

/**
 * @todo Move DataCollectorTranslator alias creation to DI Extension, and decide if we have debug mode enabled.
 */
$builder->setAlias(TranslatorInterface::class, DataCollectorTranslator::class);

$builder->setDefinition(TranslationDataCollector::class, TranslationDataCollector::class, [
    'arguments' => [
        service(DataCollectorTranslator::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

/**
 * Store list of directories with translation files.
 */
$builder->mergeParameter('translation.directory_list', []);
