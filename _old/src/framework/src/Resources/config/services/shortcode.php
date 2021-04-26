<?php declare(strict_types=1);

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Shortcode\Processor;
use Tulia\Component\Shortcode\ProcessorInterface;
use Tulia\Component\Shortcode\Registry\CompilerRegistry;
use Tulia\Component\Shortcode\Registry\CompilerRegistryInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(ProcessorInterface::class, Processor::class, [
    'arguments' => [
        service(CompilerRegistryInterface::class),
    ],
]);

$builder->setDefinition(CompilerRegistryInterface::class, CompilerRegistry::class, [
    'arguments' => [
        tagged('shortcode.compiler'),
    ],
]);

