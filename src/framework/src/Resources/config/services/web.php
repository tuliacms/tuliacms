<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Twig\Extension\RequestExtension;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(RequestStack::class, RequestStack::class);

$builder->setDefinition(RequestExtension::class, RequestExtension::class, [
    'arguments' => [
        service(RequestStack::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);
