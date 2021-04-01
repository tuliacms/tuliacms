<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Log\Logger;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Kernel\Controller\ArgumentResolver;
use Tulia\Framework\Kernel\Controller\ArgumentResolverInterface;
use Tulia\Framework\Kernel\Controller\ControllerResolver;
use Tulia\Framework\Kernel\Controller\ControllerResolverInterface;
use Tulia\Framework\Kernel\HttpKernel;
use Tulia\Framework\Kernel\HttpKernelInterface;
use Tulia\Framework\Kernel\Kernel;
use Tulia\Framework\Kernel\KernelInterface;

/** @var ContainerBuilderInterface $builder */

/*$builder->setDefinition(KernelInterface::class, Kernel::class);*/
/*$builder->setDefinition(EventDispatcherInterface::class, EventDispatcher::class, [
    'pass_tagged_lazy' => [
        'event_listener' => function (ContainerInterface $container, EventDispatcherInterface $dispatcher, string $service, array $tag) {
            $method = $tag['method'] ?? 'handle';
            $dispatcher->addListener(
                $tag['event'],
                function ($event) use ($container, $service, $method) {
                    $object = $container->get($service);

                    if (is_callable($object) || method_exists($object, '__invoke')) {
                        return $object($event);
                    }

                    return $object->{$method}($event);
                },
                $tag['priority'] ?? 10
            );
        },
    ],
]);*/

/*$builder->setAlias(SymfonyEventDispatcherInterface::class, EventDispatcherInterface::class);

$builder->setDefinition(HttpKernelInterface::class, HttpKernel::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
        service(ControllerResolverInterface::class),
        service(ArgumentResolverInterface::class),
        service(RequestStack::class),
    ]
]);*/

$builder->setDefinition(ControllerResolverInterface::class, ControllerResolver::class, [
    'arguments' => [
        service(ContainerInterface::class),
        service(ArgumentResolverInterface::class),
    ]
]);

$builder->setDefinition(ArgumentResolverInterface::class, ArgumentResolver::class, [
    'arguments' => [
        service(ContainerInterface::class),
    ]
]);

/*$builder->setDefinition(LoggerInterface::class, Logger::class);*/
