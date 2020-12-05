<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\DataCollector\ValidatorDataCollector;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(ValidatorBuilder::class, ValidatorBuilder::class);

$builder->setDefinition(ValidatorInterface::class, RecursiveValidator::class, [
    'factory' => function (ValidatorBuilder $builder, TranslatorInterface $translator, ConstraintValidatorFactoryInterface $validatorFactory) {
        $builder->setTranslationDomain('validators');
        $builder->setTranslator($translator);
        $builder->setConstraintValidatorFactory($validatorFactory);
        $builder->disableAnnotationMapping();

        return $builder->getValidator();
    },
    'arguments' => [
        service(ValidatorBuilder::class),
        service(TranslatorInterface::class),
        service(ConstraintValidatorFactoryInterface::class),
    ]
]);

$builder->setDefinition(ConstraintValidatorFactoryInterface::class, ContainerConstraintValidatorFactory::class, [
    'arguments' => [
        service(ContainerInterface::class),
    ]
]);

$builder->setDefinition(TraceableValidator::class, TraceableValidator::class, [
    'arguments' => [
        service(ValidatorInterface::class),
    ]
]);

$builder->setDefinition(ValidatorDataCollector::class, ValidatorDataCollector::class, [
    'arguments' => [
        service(TraceableValidator::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);
