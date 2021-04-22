<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Activity\Application\Command\ActivityStorage;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Cms\Filemanager\Application\ImageUrlResolver;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface as FilemanagerFinderFactory;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Node\Application\Command\NodeStorage;
use Tulia\Cms\Node\Application\Event\NodeCreatedEvent;
use Tulia\Cms\Node\Application\Event\NodeDeletedEvent;
use Tulia\Cms\Node\Application\Event\NodePreCreateEvent;
use Tulia\Cms\Node\Application\Event\NodePreDeleteEvent;
use Tulia\Cms\Node\Application\Event\NodePreUpdateEvent;
use Tulia\Cms\Node\Application\EventListener\ActivityLogger;
use Tulia\Cms\Node\Application\EventListener\BodyClass;
use Tulia\Cms\Node\Application\EventListener\ContentRenderer;
use Tulia\Cms\Node\Application\EventListener\ContentShortcodeCompiler;
use Tulia\Cms\Node\Application\EventListener\EditLinks;
use Tulia\Cms\Node\Application\EventListener\MetadataLoader;
use Tulia\Cms\Node\Application\EventListener\NodeChildrenPreDeleteValidator;
use Tulia\Cms\Node\Application\EventListener\SlugGenerator;
use Tulia\Cms\Node\Domain\RepositoryInterface;
use Tulia\Cms\Node\Infrastructure\Cms\Breadcrumbs\CrumbsResolver;
use Tulia\Cms\Node\Infrastructure\Cms\Filemanager\ImageSize\DefaultSizesProvider;
use Tulia\Cms\Node\Infrastructure\Cms\Menu\IdentityProvider;
use Tulia\Cms\Node\Infrastructure\Cms\Menu\Selector;
use Tulia\Cms\Node\Infrastructure\Cms\Menu\TypeRegistrator;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\DefaultMetadataRegistrator;
use Tulia\Cms\Node\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Node\Infrastructure\Cms\SearchAnything\SearchProvider;
use Tulia\Cms\Node\Infrastructure\Cms\Settings\SettingsFactory;
use Tulia\Cms\Node\Infrastructure\Framework\Form\FormType\NodeTypeaheadType;
use Tulia\Cms\Node\Infrastructure\Framework\Routing\Generator;
use Tulia\Cms\Node\Infrastructure\Framework\Routing\Matcher;
use Tulia\Cms\Node\Infrastructure\Framework\Twig\Extension\NodeExtension;
use Tulia\Cms\Node\Infrastructure\NodeType\DefaultTypesRegistrator;
use Tulia\Cms\Node\Infrastructure\NodeType\Registry;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\Storage\DatabaseStorage;
use Tulia\Cms\Node\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\Node\Infrastructure\Persistence\Domain\DbalPersister;
use Tulia\Cms\Node\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Event\QueryFilterEvent;
use Tulia\Cms\Node\Query\Factory\NodeFactory;
use Tulia\Cms\Node\Query\Factory\NodeFactoryInterface;
use Tulia\Cms\Node\Query\FinderFactory;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\UserInterface\Web\BackendMenu\NodeMenuBuilder;
use Tulia\Cms\Node\UserInterface\Web\Form\Extension\AuthorExtension;
use Tulia\Cms\Node\UserInterface\Web\Form\Extension\DefaultFieldsExtension;
use Tulia\Cms\Node\UserInterface\Web\Form\Extension\NodeTypeExtensionAggregate;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeFormManagerFactory;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistryInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TermFinderFactoryInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Shortcode\ProcessorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/** @var ContainerBuilderInterface $builder */

/*$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('node.type.registrator'),
        tagged('node.type.storage'),
    ],
]);*/

/*$builder->setDefinition(DatabaseStorage::class, DatabaseStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
    'tags' => [ tag('node.type.storage') ],
]);*/

/*$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(RegistryInterface::class),
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
        service(CurrentWebsiteInterface::class),
        DbalQuery::class,
    ],
]);*/

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(HydratorInterface::class),
        service(SyncerInterface::class),
    ],
]);

/*$builder->setDefinition(Generator::class, Generator::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(FrontendRouteSuffixResolver::class),
    ],
    'tags' => [ tag('router.generator') ],
]);*/

/*$builder->setDefinition(Matcher::class, Matcher::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(RegistryInterface::class),
        service(FrontendRouteSuffixResolver::class),
    ],
    'tags' => [ tag('router.matcher') ],
]);*/

/*$builder->setDefinition(DefaultTypesRegistrator::class, DefaultTypesRegistrator::class, [
    'tags' => [ tag('node.type.registrator') ],
]);*/


/*$builder->setDefinition(DefaultMetadataRegistrator::class, DefaultMetadataRegistrator::class, [
    'tags' => [ tag('metadata.registrator') ],
]);*/


/*$builder->setDefinition(IdentityProvider::class, IdentityProvider::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('menu.identity_provider') ],
]);*/

/*$builder->setDefinition(TypeRegistrator::class, TypeRegistrator::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('menu.builder.type_registrator') ],
]);*/

$builder->setDefinition(Selector::class, Selector::class, [
    'arguments' => [
        service(RegistryInterface::class),
        service(EngineInterface::class),
        service(FormFactoryInterface::class),
    ],
]);

/*$builder->setDefinition(SettingsFactory::class, SettingsFactory::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('settings.group_factory') ],
]);*/

$builder->setDefinition(Loader::class, Loader::class, [
    'arguments' => [
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(NodeFactoryInterface::class, NodeFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(Loader::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(NodeStorage::class, NodeStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(NodeFormManagerFactory::class, NodeFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(RegistryInterface::class),
        service(NodeStorage::class),
    ],
]);

/*$builder->setDefinition(SearchProvider::class, SearchProvider::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(FilemanagerFinderFactory::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
        service(RegistryInterface::class),
        service(ImageUrlResolver::class),
    ],
    'tags' => [
        tag('search.provider', 1000),
    ],
]);*/



/**
 * Event listeners.
 */
/*$builder->setDefinition(ActivityLogger::class, ActivityLogger::class, [
    'arguments' => [
        service(ActivityStorage::class),
        service(AuthenticatedUserProviderInterface::class),
        service(RouterInterface::class),
        service(RegistryInterface::class),
    ],
    'tags' => [
        tag_event_listener(NodeCreatedEvent::class, 0, 'handleCreated'),
        tag_event_listener(NodeDeletedEvent::class, 0, 'handleDeleted'),
    ],
]);*/

/*$builder->setDefinition(SlugGenerator::class, SlugGenerator::class, [
    'arguments' => [
        service(SluggerInterface::class),
        service(FinderFactoryInterface::class),
    ],
    'tags' => [
        tag_event_listener(NodePreCreateEvent::class, 1000),
        tag_event_listener(NodePreUpdateEvent::class, 1000),
    ],
]);*/

/*$builder->setDefinition(BodyClass::class, BodyClass::class, [
    'tags' => [
        tag_event_listener(CollectBodyClassEvent::class),
    ],
]);*/

/*$builder->setDefinition(EditLinks::class, EditLinks::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(RouterInterface::class),
        service(RegistryInterface::class),
    ],
    'tags' => [
        tag_event_listener(CollectEditLinksEvent::class),
    ],
]);*/

/*$builder->setDefinition(MetadataLoader::class, MetadataLoader::class, [
    'arguments' => [
        service(Loader::class),
    ],
    'tags' => [
        tag_event_listener(QueryFilterEvent::class),
    ],
]);*/

/*$builder->setDefinition(NodeChildrenPreDeleteValidator::class, NodeChildrenPreDeleteValidator::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
    'tags' => [
        tag_event_listener(NodePreDeleteEvent::class, 1000),
    ],
]);*/

/*$builder->setDefinition(ContentShortcodeCompiler::class, ContentShortcodeCompiler::class, [
    'arguments' => [
        service(ProcessorInterface::class),
    ],
    'tags' => [
        tag_event_listener(NodePreCreateEvent::class),
        tag_event_listener(NodePreUpdateEvent::class),
    ],
]);*/

/*$builder->setDefinition(ContentRenderer::class, ContentRenderer::class, [
    'arguments' => [
        service(EngineInterface::class),
        parameter('kernel.environment'),
        parameter('cms.node.finder.content_renderer.scopes'),
    ],
    'tags' => [
        tag_event_listener(QueryFilterEvent::class),
    ],
]);*/

$builder->setDefinition(NodeExtension::class, NodeExtension::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(NodeForm::class, NodeForm::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

/*$builder->setDefinition(AuthorExtension::class, AuthorExtension::class, [
    'arguments' => [
        service(AuthenticatedUserProviderInterface::class),
    ],
    'tags' => [ tag('form_extension') ],
]);*/

/*$builder->setDefinition(NodeTypeExtensionAggregate::class, NodeTypeExtensionAggregate::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('form_extension_aggregate') ],
]);*/

/*$builder->setDefinition(DefaultFieldsExtension::class, DefaultFieldsExtension::class, [
    'tags' => [ tag('form_extension') ],
]);*/

$builder->setDefinition(NodeTypeaheadType::class, NodeTypeaheadType::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

/*$builder->setDefinition(NodeMenuBuilder::class, NodeMenuBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
        service(RegistryInterface::class),
        service(TaxonomyRegistryInterface::class),
    ],
    'tags' => [ tag('backend_menu.builder') ],
]);*/

/*$builder->setDefinition(CrumbsResolver::class, CrumbsResolver::class, [
    'arguments' => [
        service(RouterInterface::class),
        service(RegistryInterface::class),
        service(FinderFactoryInterface::class),
        service(TermFinderFactoryInterface::class),
    ],
    'tags' => [ tag('breadcrumbs.resolver') ],
]);*/

/*$builder->setDefinition(DefaultSizesProvider::class, DefaultSizesProvider::class, [
    'tags' => [ tag('filemanager.image_size.provider') ],
]);*/


/**
 * List of scopes, where ContentRenderer listener will work. Allows to bind Node's
 * content rendering in many more scopes than default ones, even for custom created
 * by extensions.
 */
/*$builder->mergeParameter('cms.node.finder.content_renderer.scopes', [
    ScopeEnum::SINGLE,
    ScopeEnum::ROUTING_MATCHER,
]);*/
/*
$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/node' => dirname(__DIR__) . '/views/frontend',
    'backend/node' => dirname(__DIR__) . '/views/backend',
]);*/
