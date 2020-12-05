<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Widget\Registry\WidgetRegistry;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;
use Tulia\Component\Widget\Storage\ArrayStorage;
use Tulia\Component\Widget\Storage\StorageInterface;
use Tulia\Framework\Twig\Extension\RequestExtension;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(StorageInterface::class, ArrayStorage::class);
$builder->setDefinition(WidgetRegistryInterface::class, WidgetRegistry::class, [
    'arguments' => [
        tagged('widget'),
    ],
]);
