<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Application\Command\TermStorage;
use Tulia\Cms\Taxonomy\Application\Event\TermCreatedEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreCreateEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreUpdateEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermUpdatedEvent;
use Tulia\Cms\Taxonomy\Application\EventListener\BodyClass;
use Tulia\Cms\Taxonomy\Application\EventListener\EditLinks;
use Tulia\Cms\Taxonomy\Application\EventListener\MetadataLoader;
use Tulia\Cms\Taxonomy\Application\EventListener\PathGenerator;
use Tulia\Cms\Taxonomy\Application\EventListener\SlugGenerator;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\DefaultTypesRegistrator;
use Tulia\Cms\Taxonomy\Domain\RepositoryInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Breadcrumbs\CrumbsResolver;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu\IdentityProvider;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu\Selector;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu\TypeRegistrator;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\DefaultMetadataRegistrator;
use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Matcher;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Generator;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\FullPathStrategy;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\SimpleStrategy;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\StrategyRegistry;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Twig\Extension\TaxonomyExtension;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\DbalPersister;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\Registry;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy\DbalTermStorage;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy\TermStorageInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\DbalStorage as TermPathDbalStorage;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface as TermPathStorageInterface;
use Tulia\Cms\Taxonomy\Query\Event\QueryFilterEvent;
use Tulia\Cms\Taxonomy\Query\Factory\TermFactory;
use Tulia\Cms\Taxonomy\Query\Factory\TermFactoryInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactory;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\UI\Web\Form\Extension\DefaultFieldsExtension;
use Tulia\Cms\Taxonomy\UI\Web\Form\Extension\TaxonomyTypeExtensionAggregate;
use Tulia\Cms\Taxonomy\UI\Web\Form\TermForm;
use Tulia\Cms\Taxonomy\UI\Web\Form\TermFormManagerFactory;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Slug\SluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('taxonomy.type.registrator'),
        tagged('taxonomy.type.storage'),
    ],
]);

$builder->setDefinition(Loader::class, Loader::class, [
    'arguments' => [
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(TermFactoryInterface::class, TermFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(Loader::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
        service(CurrentWebsiteInterface::class),
        DbalQuery::class,
    ],
]);

$builder->setDefinition(DefaultTypesRegistrator::class, DefaultTypesRegistrator::class, [
    'tags' => [ tag('taxonomy.type.registrator') ],
]);

$builder->setDefinition(TermStorage::class, TermStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(TermForm::class, TermForm::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(TermFormManagerFactory::class, TermFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(RegistryInterface::class),
        service(TermStorage::class),
    ],
]);

$builder->setDefinition(DefaultFieldsExtension::class, DefaultFieldsExtension::class, [
    'tags' => [ tag('form_extension') ],
]);

$builder->setDefinition(TaxonomyTypeExtensionAggregate::class, TaxonomyTypeExtensionAggregate::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('form_extension_aggregate') ],
]);

$builder->setDefinition(TaxonomyTypeaheadType::class, TaxonomyTypeaheadType::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(RouterInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(HydratorInterface::class),
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(DefaultMetadataRegistrator::class, DefaultMetadataRegistrator::class, [
    'tags' => [ tag('metadata.registrator') ],
]);

$builder->setDefinition(MetadataLoader::class, MetadataLoader::class, [
    'arguments' => [
        service(Loader::class),
    ],
    'tags' => [
        tag_event_listener(QueryFilterEvent::class),
    ],
]);

$builder->setDefinition(SlugGenerator::class, SlugGenerator::class, [
    'arguments' => [
        service(SluggerInterface::class),
        service(FinderFactoryInterface::class),
    ],
    'tags' => [
        tag_event_listener(TermPreCreateEvent::class, 1000),
        tag_event_listener(TermPreUpdateEvent::class, 1000),
    ],
]);

$builder->setDefinition(Generator::class, Generator::class, [
    'arguments' => [
        service(TermPathStorageInterface::class),
        service(FrontendRouteSuffixResolver::class),
    ],
    'tags' => [ tag('router.generator') ],
]);

$builder->setDefinition(Matcher::class, Matcher::class, [
    'arguments' => [
        service(TermPathStorageInterface::class),
        service(FinderFactoryInterface::class),
        service(RegistryInterface::class),
        service(FrontendRouteSuffixResolver::class),
    ],
    'tags' => [ tag('router.matcher') ],
]);

$builder->setDefinition(StrategyRegistry::class, StrategyRegistry::class, [
    'arguments' => [
        tagged('taxonomy.routing.strategy'),
    ],
]);

$builder->setDefinition(SimpleStrategy::class, SimpleStrategy::class, [
    'arguments' => [
        service(TermStorageInterface::class),
    ],
    'tags' => [ tag('taxonomy.routing.strategy') ],
]);

$builder->setDefinition(FullPathStrategy::class, FullPathStrategy::class, [
    'arguments' => [
        service(TermStorageInterface::class),
    ],
    'tags' => [ tag('taxonomy.routing.strategy') ],
]);

$builder->setDefinition(EditLinks::class, EditLinks::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(RouterInterface::class),
        service(RegistryInterface::class),
    ],
    'tags' => [
        tag_event_listener(CollectEditLinksEvent::class),
    ],
]);

$builder->setDefinition(PathGenerator::class, PathGenerator::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(TermPathStorageInterface::class),
        service(StrategyRegistry::class),
        service(RegistryInterface::class),
    ],
    'tags' => [
        tag_event_listener(TermCreatedEvent::class, 500),
        tag_event_listener(TermUpdatedEvent::class, 500),
    ],
]);

$builder->setDefinition(TermPathStorageInterface::class, TermPathDbalStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(TermStorageInterface::class, DbalTermStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(IdentityProvider::class, IdentityProvider::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('menu.identity_provider') ],
]);

$builder->setDefinition(TypeRegistrator::class, TypeRegistrator::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('menu.builder.type_registrator') ],
]);

$builder->setDefinition(Selector::class, Selector::class, [
    'arguments' => [
        service(RegistryInterface::class),
        service(EngineInterface::class),
        service(FormFactoryInterface::class),
    ],
]);

$builder->setDefinition(BodyClass::class, BodyClass::class, [
    'tags' => [
        tag_event_listener(CollectBodyClassEvent::class),
    ],
]);

$builder->setDefinition(TaxonomyExtension::class, TaxonomyExtension::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(CrumbsResolver::class, CrumbsResolver::class, [
    'arguments' => [
        service(RouterInterface::class),
        service(RegistryInterface::class),
        service(FinderFactoryInterface::class),
    ],
    'tags' => [ tag('breadcrumbs.resolver') ],
]);


$builder->mergeParameter('templating.paths', [
    'cms/taxonomy' => dirname(__DIR__) . '/views/frontend',
    'backend/taxonomy' => dirname(__DIR__) . '/views/backend',
]);

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);
