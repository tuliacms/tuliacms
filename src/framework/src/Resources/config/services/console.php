<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Console\CommandLoader;

$builder->setDefinition('console.command_loader', CommandLoader::class, [
    'arguments' => [
        tagged('console.command'),
    ],
]);
