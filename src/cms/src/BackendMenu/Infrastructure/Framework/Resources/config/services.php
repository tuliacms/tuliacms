<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelper;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Application\HtmlBuilder;
use Tulia\Cms\BackendMenu\Application\HtmlBuilderInterface;
use Tulia\Cms\BackendMenu\Infrastructure\Framework\Twig\BackendMenuExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\RouterInterface;

$builder->setDefinition(HtmlBuilderInterface::class, HtmlBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
        tagged('backend_menu.builder'),
    ],
]);

$builder->setDefinition(BuilderHelperInterface::class, BuilderHelper::class, [
    'arguments' => [
        service(RequestStack::class),
        service(TranslatorInterface::class),
        service(RouterInterface::class),
    ],
]);

$builder->setDefinition(BackendMenuExtension::class, BackendMenuExtension::class, [
    'arguments' => [
        service(HtmlBuilderInterface::class),
        service(RequestStack::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);
