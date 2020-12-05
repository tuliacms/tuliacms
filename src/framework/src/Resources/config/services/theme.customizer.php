<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Component\Theme\Customizer\Builder\Builder;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Builder\Controls\Registry;
use Tulia\Component\Theme\Customizer\Builder\Controls\RegistryInterface;
use Tulia\Component\Theme\Customizer\Builder\Controls\SelectControl;
use Tulia\Component\Theme\Customizer\Builder\Controls\TextareaControl;
use Tulia\Component\Theme\Customizer\Builder\Controls\TextControl;
use Tulia\Component\Theme\Customizer\Builder\Plugin\Registry as BuilderRegistry;
use Tulia\Component\Theme\Customizer\Builder\Plugin\RegistryInterface as BuilderRegistryInterface;
use Tulia\Component\Theme\Customizer\Builder\Plugin\TranslatorPlugin;
use Tulia\Component\Theme\Customizer\Builder\ThemeBuilderFactory;
use Tulia\Component\Theme\Customizer\Builder\ThemeBuilderFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactory;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Storage\ArrayStorage;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\Customizer;
use Tulia\Component\Theme\Customizer\CustomizerInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\Customizer\RequestStackDetector;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Resolver\CustomizerResolver;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(CustomizerInterface::class, Customizer::class, [
    'arguments' => [
        service(ChangesetFactoryInterface::class),
        service(ThemeBuilderFactoryInterface::class),
        tagged('theme.customizer.provider'),
    ],
]);

$builder->setDefinition(ThemeBuilderFactoryInterface::class, ThemeBuilderFactory::class, [
    'arguments' => [
        service(ContainerInterface::class),
        parameter('theme.customizer.builder.base_class'),
    ],
]);

$builder->setDefinition(BuilderInterface::class, Builder::class, [
    'arguments' => [
        service(CustomizerInterface::class),
        service(RegistryInterface::class),
        service(BuilderRegistryInterface::class),
    ],
]);

$builder->setDefinition(BuilderRegistryInterface::class, BuilderRegistry::class, [
    'arguments' => [
        tagged('theme.customizer.builder.plugin'),
    ],
]);

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('theme.customizer.control'),
    ],
]);

$builder->setDefinition(StorageInterface::class, ArrayStorage::class);

$builder->setDefinition(CustomizerResolver::class, CustomizerResolver::class, [
    'arguments' => [
        service(ManagerInterface::class),
        service(CustomizerInterface::class),
        service(StorageInterface::class),
        service(DetectorInterface::class),
    ],
    'tags' => [ tag('theme.resolver') ],
]);

$builder->setDefinition(ChangesetFactoryInterface::class, ChangesetFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        parameter('theme.changeset.base_class'),
    ],
]);

$builder->setDefinition(TranslatorPlugin::class, TranslatorPlugin::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('theme.customizer.builder.plugin') ],
]);

$builder->setDefinition(TextControl::class, TextControl::class, [
    'tags' => [ tag('theme.customizer.control') ],
]);

$builder->setDefinition(TextareaControl::class, TextareaControl::class, [
    'tags' => [ tag('theme.customizer.control') ],
]);

$builder->setDefinition(SelectControl::class, SelectControl::class, [
    'tags' => [ tag('theme.customizer.control') ],
]);

$builder->setDefinition(DetectorInterface::class, RequestStackDetector::class, [
    'arguments' => [
        service(RequestStack::class),
    ],
]);


$builder->setParameter('theme.changeset.base_class', Changeset::class);
$builder->setParameter('theme.customizer.builder.base_class', BuilderInterface::class);
