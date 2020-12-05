<?php declare(strict_types=1);

use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\DataCollector\FormDataCollector;
use Symfony\Component\Form\Extension\DataCollector\FormDataExtractor;
use Symfony\Component\Form\Extension\DataCollector\FormDataExtractorInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Twig\Environment;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(FormFactoryBuilderInterface::class, FormFactoryBuilder::class, [
    'arguments' => [
        true,
    ],
    'pass_tagged' => [
        'form.type_extension' => 'addTypeExtension',
        'form.type'           => 'addType',
        'form.extension'      => 'addExtension',
    ],
]);

$builder->setDefinition(FormFactoryInterface::class, FormFactory::class, [
    'factory' => function (FormFactoryBuilderInterface $factoryBuilder) {
        return $factoryBuilder->getFormFactory();
    },
    'arguments' => [
        service(FormFactoryBuilderInterface::class),
    ],
]);

$builder->setDefinition(TwigRendererEngine::class, TwigRendererEngine::class, [
    'arguments' => [
        parameter('twig.layout.themes'),
        service(Environment::class),
    ],
]);

$builder->setDefinition(HttpFoundationExtension::class, HttpFoundationExtension::class, [
    'tags' => [ tag('form.extension') ],
]);

$builder->setDefinition(CsrfExtension::class, CsrfExtension::class, [
    'arguments' => [
        service(CsrfTokenManagerInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('form.extension') ],
]);

$builder->setDefinition(ValidatorExtension::class, ValidatorExtension::class, [
    'arguments' => [
        service(ValidatorInterface::class),
    ],
    'tags' => [ tag('form.extension') ],
]);

$builder->setDefinition(FormRenderer::class, FormRenderer::class, [
    'arguments' => [
        service(TwigRendererEngine::class),
        service(CsrfTokenManagerInterface::class),
    ],
    'tags' => [ tag('twig.factory_runtime_loader') ],
]);

$builder->setDefinition(FormDataExtractorInterface::class, FormDataExtractor::class);

$builder->setDefinition(FormDataCollector::class, FormDataCollector::class, [
    'arguments' => [
        service(FormDataExtractorInterface::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);
