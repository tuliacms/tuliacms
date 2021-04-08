<?php declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\DataCollector\EventDataCollector;
use Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector;
use Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\RouterDataCollector;
use Symfony\Component\HttpKernel\Profiler\FileProfilerStorage;
use Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Kernel\DataCollector\RequestDataCollector;
use Tulia\Framework\Kernel\DataCollector\TimeDataCollector;
use Tulia\Framework\Kernel\Event\ControllerEvent;
use Tulia\Framework\Kernel\KernelInterface;
use Tulia\Framework\Kernel\Profiler\Profiler;
use Tulia\Framework\Kernel\Event\ExceptionEvent;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Kernel\Event\TerminateEvent;
use Tulia\Framework\Kernel\EventListener\ProfilerListener;
use Tulia\Framework\Twig\Extension\ProfilerExtension;
use Twig\Environment;
use Twig\Profiler\Profile;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(Profiler::class, Profiler::class, [
    'arguments' => [
        service(ProfilerStorageInterface::class),
        service(LoggerInterface::class),
    ],
    'calls' => [
        call('setCollectors', [ tagged('profiler.data_collector') ]),
    ],
]);

$builder->setDefinition(ProfilerStorageInterface::class, FileProfilerStorage::class, [
    'factory' => function (string $cacheDir) {
        return new FileProfilerStorage('file:' . $cacheDir . '/profiler');
    },
    'arguments' => [
        parameter('kernel.cache_dir')
    ],
]);

$builder->setDefinition(ProfilerListener::class, ProfilerListener::class, [
    'arguments' => [
        service(Profiler::class),
        service(RequestStack::class),
    ],
    'tags' => [
        tag_event_listener(ResponseEvent::class, -1000, 'onKernelResponse'),
        tag_event_listener(ExceptionEvent::class, -1000, 'onKernelException'),
        tag_event_listener(TerminateEvent::class, -1000, 'onKernelTerminate'),
    ],
]);

$builder->setDefinition(LoggerDataCollector::class, LoggerDataCollector::class, [
    'arguments' => [
        service(LoggerInterface::class),
        null,
        service(RequestStack::class)
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(RouterDataCollector::class, RouterDataCollector::class, [
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(RequestDataCollector::class, RequestDataCollector::class, [
    'tags' => [
        tag('profiler.data_collector'),
        tag_event_listener(ControllerEvent::class, 0, 'saveControllerFromEvent'),
    ],
]);

$builder->setDefinition(MemoryDataCollector::class, MemoryDataCollector::class, [
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(ExceptionDataCollector::class, ExceptionDataCollector::class, [
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(EventDataCollector::class, EventDataCollector::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(TimeDataCollector::class, TimeDataCollector::class, [
    'arguments' => [
        service(KernelInterface::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(TwigDataCollector::class, TwigDataCollector::class, [
    'arguments' => [
        service(Profile::class),
        service(Environment::class),
    ],
    'tags' => [ tag('profiler.data_collector') ],
]);

$builder->setDefinition(ProfilerExtension::class, ProfilerExtension::class, [
    'arguments' => [
        parameter('kernel.project_dir'),
    ],
    'tags' => [ tag('twig.extension') ],
]);
