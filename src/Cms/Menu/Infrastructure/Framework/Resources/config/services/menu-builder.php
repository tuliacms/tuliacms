<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Cms\Menu\Infrastructure\Builder\Builder;
use Tulia\Cms\Menu\Infrastructure\Builder\BuilderInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyBuilder;
use Tulia\Cms\Menu\Infrastructure\Builder\Hierarchy\HierarchyBuilderInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder\HtmlBuilder;
use Tulia\Cms\Menu\Infrastructure\Builder\HtmlBuilder\HtmlBuilderInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Providers\HomepageProvider;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Providers\UrlProvider;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Registry as IdentityRegistry;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\RegistryInterface as IdentityRegistryInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\DefaultTypesRegistrator;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\Registry as TypeRegistry;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface as TypeRegistryInterface;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

/*$builder->setDefinition(IdentityRegistryInterface::class, IdentityRegistry::class, [
    'arguments' => [
        tagged('menu.identity_provider'),
    ],
]);*/

$builder->setDefinition(HierarchyBuilderInterface::class, HierarchyBuilder::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(IdentityRegistryInterface::class),
    ],
]);

$builder->setDefinition(BuilderInterface::class, Builder::class, [
    'arguments' => [
        service(HierarchyBuilderInterface::class),
        service(HtmlBuilderInterface::class),
    ],
]);

$builder->setDefinition(HtmlBuilderInterface::class, HtmlBuilder::class);

/*$builder->setDefinition(TypeRegistryInterface::class, TypeRegistry::class, [
    'arguments' => [
        tagged('menu.builder.type_registrator'),
    ],
]);*/

/*$builder->setDefinition(DefaultTypesRegistrator::class, DefaultTypesRegistrator::class, [
    'tags' => [ tag('menu.builder.type_registrator') ],
]);*/



/*$builder->setDefinition(HomepageProvider::class, HomepageProvider::class, [
    'arguments' => [
        service(RouterInterface::class),
        'homepage',
    ],
    'tags' => [ tag('menu.identity_provider') ],
]);

$builder->setDefinition(UrlProvider::class, UrlProvider::class, [
    'tags' => [ tag('menu.identity_provider') ],
]);*/
