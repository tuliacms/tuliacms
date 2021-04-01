<?php declare(strict_types=1);

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\HomepageResolver;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\Registry;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\RegistryInterface;
use Tulia\Cms\Breadcrumbs\Application\GeneratorInterface;
use Tulia\Cms\Breadcrumbs\Application\Generator;
use Tulia\Cms\Breadcrumbs\Infrastructure\Framework\Twig\Extension\BreadcrumbsExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;

/** @var ContainerBuilderInterface $builder */

/*$builder->setDefinition(BreadcrumbsExtension::class, BreadcrumbsExtension::class, [
    'arguments' => [
        service(GeneratorInterface::class),
        service(DocumentInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);*/

$builder->setDefinition(GeneratorInterface::class, Generator::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
]);

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('breadcrumbs.resolver'),
    ],
]);

$builder->setDefinition(HomepageResolver::class, HomepageResolver::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(RouterInterface::class),
    ],
    'tags' => [ tag('breadcrumbs.resolver') ],
]);
