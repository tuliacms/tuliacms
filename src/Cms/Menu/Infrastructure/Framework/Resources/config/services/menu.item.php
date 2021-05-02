<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Application\Command\ItemStorage;
use Tulia\Cms\Menu\Application\Command\MenuStorage;
use Tulia\Cms\Menu\Domain\WriteModel\Model\MenuRepositoryInterface;
use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\Loader;
use Tulia\Cms\Menu\Infrastructure\Cms\Metadata\Item\LoaderInterface;
use Tulia\Cms\Menu\Infrastructure\Cms\SearchAnything\SearchProvider;
use Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType\MenuItemChoiceType;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item\DbalPersister;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item\DbalRepository;
use Tulia\Cms\Menu\Infrastructure\Persistence\Query\Item\DatatableFinder;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;
use Tulia\Cms\Menu\UserInterface\Web\Form\MenuItemFormManagerFactory;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface as TypeRegistry;
use Tulia\Cms\Menu\UserInterface\Web\Form\MenuItemForm;

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(DbalRepository::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(CurrentWebsiteInterface::class),
        service(HydratorInterface::class),
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(LoaderInterface::class, Loader::class, [
    'arguments' => [
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(MenuItemFormManagerFactory::class, MenuItemFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(ItemStorage::class),
    ],
]);

$builder->setDefinition(ItemStorage::class, ItemStorage::class, [
    'arguments' => [
        service(MenuRepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

/*$builder->setDefinition(SearchProvider::class, SearchProvider::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(RouterInterface::class),
    ],
    'tags' => [ tag('search.provider', 500) ],
]);*/

/*$builder->setDefinition(MenuItemChoiceType::class, MenuItemChoiceType::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(MenuItemForm::class, MenuItemForm::class, [
    'arguments' => [
        service(TypeRegistry::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);*/

$builder->setDefinition(DatatableFinder::class, DatatableFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
        service(CsrfTokenManagerInterface::class),
    ],
]);
