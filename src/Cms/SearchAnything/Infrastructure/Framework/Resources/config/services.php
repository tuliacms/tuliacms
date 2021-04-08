<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Cms\SearchAnything\Factory\EngineFactory;
use Tulia\Cms\SearchAnything\Factory\EngineFactoryInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

$builder->setDefinition(EngineFactoryInterface::class, EngineFactory::class, [
    'arguments' => [
        tagged('search.provider'),
    ],
]);
