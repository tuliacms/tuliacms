<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistry as MetadataRegistry;
use Tulia\Cms\Metadata\Domain\Registry\ContentFieldsRegistryInterface as MetadataRegistryInterface;
use Tulia\Cms\Metadata\Storage\DatabaseStorage as MetadataDatabaseStorage;
use Tulia\Cms\Metadata\Storage\StorageInterface as MetadataStorageInterface;
use Tulia\Cms\Metadata\Syncer\DatabaseStorageSyncer;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Options\Application\EventListener\CreateOptionsForNewWebsite;
use Tulia\Cms\Options\Domain\ReadModel\Options;
use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Options\Application\Service\RegisteredOptionsCollector;
use Tulia\Cms\Options\Infrastructure\Persistence\ReadModel\Options\DbalOptionsFinder;
use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\ReadModel\OptionsFinderInterface;
use Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository\DbalOptionsRepository;
use Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository\OptionsRepositoryInterface;
use Tulia\Cms\Options\Infrastructure\Framework\Twig\Extension\OptionsExtension;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\PsrEventDispatcher;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\OcramiusHydrator;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\EventListener\ForwardSlashBackendFixer;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Cms\Platform\Infrastructure\Mail\Swiftmailer;
use Tulia\Cms\Platform\Infrastructure\Utilities\DateTime\DateFormatterInterface;
use Tulia\Cms\Platform\Infrastructure\Utilities\DateTime\DateFormatterTranslatorAware;
use Tulia\Cms\Platform\Infrastructure\Utilities\DateTime\OptionsFormatterFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension\DatetimeExtension;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormSkeleton\Builder\BootstrapAccordionGroupBuilder;
use Tulia\Component\FormSkeleton\Builder\BootstrapTabsGroupBuilder;
use Tulia\Component\FormSkeleton\Builder\Builder;
use Tulia\Component\FormSkeleton\Builder\BuilderInterface;
use Tulia\Component\FormSkeleton\Extension\FormRestExtension;
use Tulia\Component\FormSkeleton\Form\ManagerFactory;
use Tulia\Component\FormSkeleton\Form\ManagerFactoryInterface;
use Tulia\Component\FormSkeleton\Extension\ExtensionRegistry;
use Tulia\Component\FormSkeleton\Extension\ExtensionRegistryInterface;
use Tulia\Component\FormSkeleton\Twig\Extension\FormExtension;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\Extension\BootstrapCheckboxRadioStyleExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\Extension\DefaultFormAttributesExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension\DocumentExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension\UtilsExtension;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Cms\Platform\UI\CLI\Command\AssetsPublish;
use Tulia\Framework\Kernel\Event\RequestEvent;

/** @var ContainerBuilderInterface $builder */

/*$services
    ->set(UtilsExtension::class)->end()

    ->set(TypeaheadType::class)
        ->arg(service(RouterInterface::class))
        ->tag('form.type')
    ->end()

    ->set(DateFormatterInterface::class, DateFormatterTranslatorAware::class)
        ->factory([OptionsFormatterFactory::class, 'factory'])
        ->arg(service(Options::class))
        ->arg(service(TranslatorInterface::class))
        ->arg(tagged('metadata.registrator'))
        ->arg(param('metadata.registrator'))
    ->end()
;*/

/*$builder->mergeParameter('assets', include __DIR__ . '/assets.php');*/

$builder->setDefinition(TypeaheadType::class, TypeaheadType::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(UtilsExtension::class, UtilsExtension::class, [
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(DocumentExtension::class, DocumentExtension::class, [
    'arguments' => [
        service(DocumentInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(EventBusInterface::class, PsrEventDispatcher::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
    ],
]);

$builder->setDefinition(HydratorInterface::class, OcramiusHydrator::class);

/*$builder->setDefinition(DateFormatterInterface::class, DateFormatterTranslatorAware::class, [
    'factory' => [OptionsFormatterFactory::class, 'factory'],
    'arguments' => [
        service(Options::class),
        service(TranslatorInterface::class),
    ],
]);*/

$builder->setDefinition(DatetimeExtension::class, DatetimeExtension::class, [
    'arguments' => [
        service(DateFormatterInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

/*$builder->setDefinition(MetadataRegistryInterface::class, MetadataRegistry::class, [
    'arguments' => [
        tagged('metadata.registrator'),
    ],
]);*/

/*$builder->setDefinition(SyncerInterface::class, DatabaseStorageSyncer::class, [
    'arguments' => [
        service(MetadataStorageInterface::class),
        service(MetadataRegistryInterface::class),
    ],
]);*/

/*$builder->setDefinition(MetadataStorageInterface::class, MetadataDatabaseStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(UuidGeneratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);*/

$builder->setDefinition(OptionsExtension::class, OptionsExtension::class, [
    'arguments' => [
        service(Options::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);


/*$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('form_extension'),
        tagged('form_extension_aggregate'),
    ],
]);*/

$builder->setDefinition(ManagerFactoryInterface::class, ManagerFactory::class, [
    'arguments' => [
        service(FormFactoryInterface::class),
        service(RegistryInterface::class),
    ],
]);

/*$builder->setDefinition(BuilderInterface::class, Builder::class, [
    'arguments' => [
        tagged('form_extension.group_builder'),
    ],
]);*/

/*$builder->setDefinition(FormRestExtension::class, FormRestExtension::class, [
    'tags' => [ tag('form_extension') ],
]);*/

/*$builder->setDefinition(BootstrapAccordionGroupBuilder::class, BootstrapAccordionGroupBuilder::class, [
    'tags' => [ tag('form_extension.group_builder') ],
]);

$builder->setDefinition(BootstrapTabsGroupBuilder::class, BootstrapTabsGroupBuilder::class, [
    'tags' => [ tag('form_extension.group_builder') ],
]);*/

/*$builder->setDefinition(FormExtension::class, FormExtension::class, [
    'arguments' => [
        service(BuilderInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);*/

$builder->setDefinition(ForwardSlashBackendFixer::class, ForwardSlashBackendFixer::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 1000),
    ],
]);

$builder->setDefinition(DefaultFormAttributesExtension::class, DefaultFormAttributesExtension::class, [
    'tags' => [ tag('form.type_extension') ],
]);

$builder->setDefinition(MailerInterface::class, Swiftmailer::class, [
    'arguments' => [
        service(Options::class),
    ],
]);

$builder->setDefinition(BootstrapCheckboxRadioStyleExtension::class, BootstrapCheckboxRadioStyleExtension::class, [
    'tags' => [ tag('form.type_extension') ],
]);

$builder->setDefinition(OptionsRepositoryInterface::class, DbalOptionsRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(HydratorInterface::class),
    ],
]);

$builder->setDefinition(OptionsFinderInterface::class, DbalOptionsFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(Options::class, Options::class, [
    'arguments' => [
        service(OptionsFinderInterface::class),
        service(OptionsRepositoryInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(WebsitesOptionsRegistrator::class, WebsitesOptionsRegistrator::class, [
    'arguments' => [
        service(RegisteredOptionsCollector::class),
        service(OptionsRepositoryInterface::class),
    ],
]);

$builder->setDefinition(RegisteredOptionsCollector::class, RegisteredOptionsCollector::class, [
    'arguments' => [
        parameter('settings.providers'),
    ],
]);

$builder->setDefinition(CreateOptionsForNewWebsite::class, CreateOptionsForNewWebsite::class, [
    'arguments' => [
        service(WebsitesOptionsRegistrator::class),
    ],
    'tags' => [ tag_event_listener(WebsiteCreated::class) ],
]);

/*$builder->setDefinition(AssetsPublisher::class, AssetsPublisher::class, [
    'arguments' => [
        parameter('kernel.public_dir'),
        parameter('public.paths'),
    ],
]);

$builder->setDefinition(AssetsPublish::class, AssetsPublish::class, [
    'arguments' => [
        service(AssetsPublisher::class),
        parameter('public.paths'),
        parameter('kernel.project_dir'),
    ],
    'tags' => [
        tag_console_command('assets:publish'),
    ],
]);*/

/*$builder->setDefinition(BootstrapAccordionGroupBuilder::class, BootstrapAccordionGroupBuilder::class, [
    'arguments' => [
        'sidebar',
        <<<EOF
{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
<div class="accordion-section">
    <div class="accordion-section-button{sectionActiveTab}" data-toggle="collapse" data-target="#form-collapse-{sectionId}">
        {sectionFields}
        {{ '{sectionLabel}'|trans({}, '{sectionTranslationDomain}') }}
        {{ badge.errors_count(form, fields|default([])) }}
    </div>
    <div id="form-collapse-{sectionId}" class="collapse{sectionActiveContent}">
        <div class="accordion-section-body">
            {sectionView}
        </div>
    </div>
</div>
EOF
    ],
    'tags' => [ tag('form_extension.group_builder') ],
]);*/

/*$builder->setDefinition(BootstrapTabsGroupBuilder::class, BootstrapTabsGroupBuilder::class, [
    'arguments' => [
        'default',
        <<<EOF
{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
<li class="nav-item">
    <a class="nav-link{sectionActive}" data-toggle="tab" href="#tab-{sectionId}">
        {sectionFields}
        {{ '{sectionLabel}'|trans({}, '{sectionTranslationDomain}') }}
        {{ badge.errors_count(form, fields|default([])) }}
    </a>
</li>
EOF
    ],
    'tags' => [ tag('form_extension.group_builder') ],
]);*/



/** Content */
include __DIR__ . '/../../../../../Node/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Menu/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Website/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../User/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Widget/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Taxonomy/Infrastructure/Framework/Resources/config/services.php';

/** Modules */
include __DIR__ . '/../../../../../WysiwygEditor/Core/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Filemanager/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Settings/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Theme/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Homepage/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Security/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Profiler/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../ContactForms/Infrastructure/Framework/Resources/config/services.php';

/** Helpers */
include __DIR__ . '/../../../../../Activity/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../BodyClass/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../BackendMenu/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Breadcrumbs/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../EditLinks/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../SearchAnything/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Installator/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../Dashboard/Infrastructure/Framework/Resources/config/services.php';
include __DIR__ . '/../../../../../FrontendToolbar/Infrastructure/Framework/Resources/config/services.php';

$builder->mergeParameter('twig.loader.filesystem.paths', [
    dirname(__DIR__) . '/views/Form',
]);
$builder->mergeParameter('twig.layout.themes', [
    'cms_form_layout.tpl',
]);

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('migrations.paths', [
    'Tulia\Cms\Migrations' => dirname(__DIR__, 6) . '/migrations',
]);

$builder->mergeParameter('public.paths', [
    dirname(__DIR__, 5) . '/FrontendToolbar/Infrastructure/Framework/Resources/public/dist' => '/core/frontend-toolbar',
]);
