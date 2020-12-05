<?php declare(strict_types=1);

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/installator' => dirname(__DIR__) . '/views',
]);
