<?php declare(strict_types=1);

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

include __DIR__ . '/services/menu.php';
include __DIR__ . '/services/menu-builder.php';
include __DIR__ . '/services/menu.item.php';

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/menu' => dirname(__DIR__) . '/views/frontend',
    'backend/menu' => dirname(__DIR__) . '/views/backend',
]);
