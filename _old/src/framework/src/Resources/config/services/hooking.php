<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Hooking\Hooker;
use Tulia\Component\Hooking\HookerInterface;
use Tulia\Component\Hooking\TwigBridge\HookingExtension;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(HookerInterface::class, Hooker::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
    ],
    'pass_tagged' => [
        'hook' => function (HookerInterface $hooker, object $service, array $tag) {
            if (isset($tag['method'])) {
                if (isset($tag['action'])) {
                    $hooker->registerAction($tag['action'], [ $service, $tag['method'] ], $tag['priority'] ?? 10);
                } elseif (isset($tag['filter'])) {
                    $hooker->registerFilter($tag['filter'], [ $service, $tag['method'] ], $tag['priority'] ?? 10);
                } else {
                    throw new \InvalidArgumentException(sprintf('Hook must provide action or filter name to bind. Missing in service %s.', $service));
                }
            }
        }
    ],
]);

$builder->setDefinition(HookingExtension::class, HookingExtension::class, [
    'arguments' => [
        service(HookerInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);
