<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\FrontendToolbar\Application\Builder\Builder;
use Tulia\Cms\FrontendToolbar\Application\EventListener\ToolbarRenderer;
use Tulia\Cms\FrontendToolbar\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\FrontendToolbar\Application\Helper\Helper;
use Tulia\Cms\FrontendToolbar\Application\Helper\HelperInterface;
use Tulia\Cms\FrontendToolbar\Application\Links\ProviderRegistry;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Framework\Kernel\Event\ResponseEvent;

$builder->setDefinition(ProviderRegistry::class, ProviderRegistry::class, [
    'arguments' => [
        tagged('frontend_toolbar.links.provider'),
    ],
]);

$builder->setDefinition(Builder::class, Builder::class, [
    'arguments' => [
        service(ProviderRegistry::class),
        service(EngineInterface::class),
    ],
]);

$builder->setDefinition(ToolbarRenderer::class, ToolbarRenderer::class, [
    'arguments' => [
        service(Builder::class),
        service(AuthorizationCheckerInterface::class),
    ],
    'tags' => [
        tag_event_listener(ResponseEvent::class),
    ],
]);

$builder->setDefinition(HelperInterface::class, Helper::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(RouterInterface::class),
        service(EngineInterface::class),
    ],
]);


$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/frontend_toolbar' => dirname(__DIR__) . '/views/frontend',
]);
