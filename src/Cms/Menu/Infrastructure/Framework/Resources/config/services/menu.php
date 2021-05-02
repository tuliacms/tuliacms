<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Application\Command\MenuStorage;
use Tulia\Cms\Menu\Domain\WriteModel\Model\MenuRepositoryInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\BuilderInterface;
use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\LoaderInterface;
use Tulia\Cms\Menu\Infrastructure\Framework\Twig\Extension\MenuExtension;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Menu\DataTransformer;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Menu\DbalMenuRepository;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Menu\DatatableFinder;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Menu\DbalQuery;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item\DbalRepository as ItemDbalRepository;
use Tulia\Cms\Menu\Application\Query\Finder\Factory\MenuFactory;
use Tulia\Cms\Menu\Application\Query\Finder\Factory\MenuFactoryInterface;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactory;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\Menu\Application\Event\MenuCreatedEvent;
use Tulia\Cms\Menu\Application\Event\MenuDeletedEvent;
use Tulia\Cms\Menu\Application\Event\MenuUpdatedEvent;
use Tulia\Cms\Menu\UserInterface\Web\BackendMenu\MenuMenuBuilder;

/*$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        DbalQuery::class,
    ],
]);*/

$builder->setDefinition(MenuRepositoryInterface::class, DbalMenuRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DataTransformer::class),
        service(ItemDbalRepository::class),
    ],
]);

$builder->setDefinition(DataTransformer::class, DataTransformer::class, [
    'arguments' => [
        service(HydratorInterface::class),
    ],
]);

$builder->setDefinition(MenuFactoryInterface::class, MenuFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(LoaderInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(MenuStorage::class, MenuStorage::class, [
    'arguments' => [
        service(MenuRepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(MenuExtension::class, MenuExtension::class, [
    'arguments' => [
        service(BuilderInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

/*$builder->setDefinition(MenuMenuBuilder::class, MenuMenuBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
        service(FinderFactoryInterface::class),
        service(RequestStack::class),
        service(CurrentWebsiteInterface::class),
    ],
    'tags' => [
        tag('backend_menu.builder'),
        tag_event_listener(MenuCreatedEvent::class, 0, 'clearCache'),
        tag_event_listener(MenuUpdatedEvent::class, 0, 'clearCache'),
        tag_event_listener(MenuDeletedEvent::class, 0, 'clearCache'),
    ],
]);*/

$builder->setDefinition(DatatableFinder::class, DatatableFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
]);
