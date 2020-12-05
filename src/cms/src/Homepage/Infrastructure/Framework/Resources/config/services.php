<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\Homepage\Infrastructure\Cms\BackendMenu\DefaultCmsMenuBuilder;
use Tulia\Cms\Dashboard\Tiles\Event\CollectTilesEvent;
use Tulia\Cms\Homepage\Infrastructure\Framework\Twig\Extension\HomepageExtension;
use Tulia\Cms\Homepage\UI\Web\Tiles\SystemTiles;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\RouterInterface;

$builder->setDefinition(SystemTiles::class, SystemTiles::class, [
    'arguments' => [
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [
        tag_event_listener(CollectTilesEvent::class),
    ],
]);

$builder->setDefinition(DefaultCmsMenuBuilder::class, DefaultCmsMenuBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
    ],
    'tags' => [ tag('backend_menu.builder') ],
]);

$builder->setDefinition(HomepageExtension::class, HomepageExtension::class, [
    'arguments' => [
        service(RequestStack::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->mergeParameter('templating.paths', [
    'cms/homepage' => dirname(__DIR__) . '/views/frontend',
    'backend/homepage' => dirname(__DIR__) . '/views/backend',
]);
