<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\ContactForms\Application\Builder\Builder;
use Tulia\Cms\ContactForms\Application\Builder\BuilderInterface;
use Tulia\Cms\ContactForms\Application\Builder\Form;
use Tulia\Cms\ContactForms\Application\Command\FormStorage;
use Tulia\Cms\ContactForms\Application\FieldsTemplate\EventListener\FieldsTemplateChangedListener;
use Tulia\Cms\ContactForms\Application\FieldsParser\FieldsParser;
use Tulia\Cms\ContactForms\Application\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForms\Application\FieldsTemplate\Service\FieldsTemplateViewUpdater;
use Tulia\Cms\ContactForms\Application\FieldType\Registry as FieldTypeRegistry;
use Tulia\Cms\ContactForms\Application\FieldType\RegistryInterface as FieldTypeRegistryInterface;
use Tulia\Cms\ContactForms\Application\FieldType\Parser\Registry as FieldParserRegistry;
use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface as FieldParserRegistryInterface;
use Tulia\Cms\ContactForms\Application\Sender\FormDataExtractor;
use Tulia\Cms\ContactForms\Application\Sender\FormDataExtractorInterface;
use Tulia\Cms\ContactForms\Application\Sender\Sender;
use Tulia\Cms\ContactForms\Application\Sender\SenderInterface;
use Tulia\Cms\ContactForms\Domain\Event\FieldsTemplateChanged;
use Tulia\Cms\ContactForms\Domain\Policy\DefaultFieldsTemplatePolicy;
use Tulia\Cms\ContactForms\Domain\Policy\FieldsTemplatePolicyInterface;
use Tulia\Cms\ContactForms\Domain\RepositoryInterface;
use Tulia\Cms\ContactForms\Infrastructure\Cms\SearchAnything\SearchProvider;
use Tulia\Cms\ContactForms\Infrastructure\Cms\Widget\Predefined\ContactFormForm;
use Tulia\Cms\ContactForms\Infrastructure\Cms\Widget\Predefined\ContactFormWidget;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalFieldsTemplate;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalFormStorage;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalFieldPersister;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\SubjectFinderPlugin;
use Tulia\Cms\ContactForms\Query\Factory\FormFactory;
use Tulia\Cms\ContactForms\Query\FinderFactory;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\ContactForms\UI\Web\Form\FormManagerFactory;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Cms\ContactForms\Infrastructure\Cms\BackendMenu\MenuBuilder;
use Tulia\Cms\ContactForms\Infrastructure\Cms\Shortcode\ContactForm;
use Tulia\Cms\ContactForms\Infrastructure\Twig\Extension\FormExtension;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\ContactForms\Application\FieldType\Core as FieldType;

/*$builder->setDefinition(MenuBuilder::class, MenuBuilder::class, [
    'arguments' => [
        '@' . BuilderHelperInterface::class,
    ],
    'tags' => [ tag('backend_menu.builder') ],
]);*/

/*$builder->setDefinition(ContactForm::class, ContactForm::class, [
    'tags' => [ tag('shortcode.compiler') ],
]);*/

/*$builder->setDefinition(FieldParserRegistryInterface::class, FieldParserRegistry::class, [
    'arguments' => [
        tagged('cms.form.field_parser'),
    ],
]);*/

$builder->setDefinition(FieldsParserInterface::class, FieldsParser::class, [
    'arguments' => [
        service(FieldParserRegistryInterface::class),
    ],
]);

/*$builder->setDefinition(FieldTypeRegistryInterface::class, FieldTypeRegistry::class, [
    'arguments' => [
        tagged('cms.form.field_type'),
    ],
]);*/

$builder->setDefinition(BuilderInterface::class, Builder::class, [
    'arguments' => [
        service(FormFactoryInterface::class),
        service(RouterInterface::class),
    ],
]);

$builder->setDefinition(SenderInterface::class, Sender::class, [
    'arguments' => [
        service(MailerInterface::class),
        service(EngineInterface::class),
    ],
]);

/*$builder->setDefinition(Form::class, Form::class, [
    'arguments' => [
        service(FieldTypeRegistryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);*/

$builder->setDefinition(FormExtension::class, FormExtension::class, [
    'arguments' => [
        service(BuilderInterface::class),
        service(FinderFactoryInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

/*$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
        service(CurrentWebsiteInterface::class),
        DbalQuery::class,
    ],
]);*/

/*$builder->setDefinition(SearchProvider::class, SearchProvider::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [
        tag('search.provider', 600),
    ],
]);*/

$builder->setDefinition(FormManagerFactory::class, FormManagerFactory::class, [
    'arguments' => [
        service(FormFactoryInterface::class),
        service(FormStorage::class),
    ],
]);

$builder->setDefinition(FormStorage::class, FormStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
        service(FieldsTemplatePolicyInterface::class),
    ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalFormStorage::class),
        service(DbalFieldPersister::class),
        service(HydratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(DbalFormStorage::class, DbalFormStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(DbalFieldPersister::class, DbalFieldPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(DbalFieldsTemplate::class, DbalFieldsTemplate::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

/*$builder->setDefinition(FieldsTemplateChangedListener::class, FieldsTemplateChangedListener::class, [
    'arguments' => [
        service(FieldsTemplateViewUpdater::class),
    ],
    'tags' => [
        tag_event_listener(FieldsTemplateChanged::class),
    ],
]);*/

$builder->setDefinition(FieldsTemplateViewUpdater::class, FieldsTemplateViewUpdater::class, [
    'arguments' => [
        service(FieldsParserInterface::class),
        service(DbalFieldPersister::class),
        service(CurrentWebsiteInterface::class),
        service(DbalFieldsTemplate::class),
    ],
]);

$builder->setDefinition(DatatableFinder::class, DatatableFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
]);

$builder->setDefinition(\Tulia\Cms\ContactForms\Query\Factory\FormFactoryInterface::class, FormFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(FieldsTemplatePolicyInterface::class, DefaultFieldsTemplatePolicy::class, [
    'arguments' => [
        service(FieldsParserInterface::class),
    ],
]);

$builder->setDefinition(FormDataExtractorInterface::class, FormDataExtractor::class, [
    'arguments' => [
        service(FieldTypeRegistryInterface::class),
    ],
]);

/*$builder->setDefinition(ContactFormWidget::class, ContactFormWidget::class, [
    'tags' => [ tag('widget') ],
]);*/

$builder->setDefinition(ContactFormForm::class, ContactFormForm::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

/*$builder->setDefinition(FieldType\TextType::class, FieldType\TextType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\TextParser::class, FieldType\TextParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\TextareaType::class, FieldType\TextareaType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\TextareaParser::class, FieldType\TextareaParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\CheckboxType::class, FieldType\CheckboxType::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\CheckboxParser::class, FieldType\CheckboxParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\EmailType::class, FieldType\EmailType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\EmailParser::class, FieldType\EmailParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\SelectType::class, FieldType\SelectType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\SelectParser::class, FieldType\SelectParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\RadioType::class, FieldType\RadioType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\RadioParser::class, FieldType\RadioParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\ConsentType::class, FieldType\ConsentType::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\ConsentParser::class, FieldType\ConsentParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/
/*$builder->setDefinition(FieldType\SubmitType::class, FieldType\SubmitType::class, [
    'tags' => [ tag('cms.form.field_type') ],
]);*/
/*$builder->setDefinition(FieldType\SubmitParser::class, FieldType\SubmitParser::class, [
    'tags' => [ tag('cms.form.field_parser') ],
]);*/



/*$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'backend/forms' => dirname(__DIR__) . '/views/backend',
]);*/

/*$builder->mergeParameter('twig.loader.array.templates', [
    'render_form' => "
        <div style=\"position:relative;\" class=\"contact-form-anchor\">
            <div id=\"anchor_{{ form.vars.id }}\" style=\"display:block;position:absolute;left:0;top:-100px\"></div>
        </div>
        {% for messages in get_flashes(['cms.form.submit_success']) %}
            {% for message in messages %}
                <div class=\"alert alert-success alert-dismissible fade show\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span>&times;</span></button>
                    {{ message|raw }}
                </div>
            {% endfor %}
        {% endfor %}
        {% for messages in get_flashes(['cms.form.submit_failed']) %}
            {% for message in messages %}
                <div class=\"alert alert-warning alert-dismissible fade show\">
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span>&times;</span></button>
                    {{ message|raw }}
                </div>
            {% endfor %}
        {% endfor %}
        {{ form_start(form) }}
        {{ include(template_from_string(template, template_name)) }}
        {{ form_errors(form) }}
        {{ form_end(form) }}
    ",
]);*/
