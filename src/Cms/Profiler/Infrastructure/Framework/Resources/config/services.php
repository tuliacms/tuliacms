<?php declare(strict_types=1);

use Tulia\Cms\Profiler\Infrastructure\Framework\EventListener\Toolbar;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Kernel\Profiler\Profiler;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(Toolbar::class, Toolbar::class, [
    'arguments' => [
        service(Profiler::class),
        service(RouterInterface::class),
        service(ContentSecurityPolicyInterface::class),
        service(EngineInterface::class),
    ],
    'tags' => [ tag_event_listener(ResponseEvent::class, -2000) ],
]);

$builder->mergeParameter('templating.paths', [
    'backend/profiler' => dirname(__DIR__) . '/views/backend',
]);

$builder->mergeParameter('profiler.templates', [
    'request' => '@backend/profiler/profiler/panel/request.tpl',
    'memory' => '@backend/profiler/profiler/panel/memory.tpl',
    'time' => '@backend/profiler/profiler/panel/time.tpl',
    'twig' => '@backend/profiler/profiler/panel/twig.tpl',
    'translation' => '@backend/profiler/profiler/panel/translation.tpl',
]);
