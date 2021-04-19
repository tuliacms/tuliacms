<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\BuilderInterface;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface as MenuFinderFactory;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Widget\Application\Command\WidgetStorage;
use Tulia\Cms\Widget\Infrastructure\Cms\SearchAnything\SearchProvider;
use Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Features\FeaturesWidget;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\Widget\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\Widget\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\DbalPersister;
use Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Menu\MenuForm;
use Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Menu\MenuWidget;
use Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Text\TextWidget;
use Tulia\Cms\Widget\Query\Factory\WidgetFactoryInterface;
use Tulia\Cms\Widget\Query\Factory\WidgetFactory;
use Tulia\Cms\Widget\Query\FinderFactory;
use Tulia\Cms\Widget\Query\FinderFactoryInterface;
use Tulia\Cms\Widget\Application\Renderer\Renderer;
use Tulia\Cms\Widget\Application\Renderer\RendererInterface;
use Tulia\Cms\Widget\Infrastructure\Framework\Twig\Extension\WidgetExtension;
use Tulia\Cms\Widget\Infrastructure\Widget\Storage\DatabaseStorage;
use Tulia\Cms\Widget\Domain\RepositoryInterface;
use Tulia\Cms\Widget\UI\Web\Form\Extension\DefaultFieldsExtension;
use Tulia\Cms\Widget\UI\Web\Form\WidgetForm;
use Tulia\Cms\Widget\UI\Web\Form\WidgetFormManagerFactory;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\Config;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;
use Tulia\Component\Widget\Storage\StorageInterface;
use Tulia\Framework\Database\ConnectionInterface;
use Twig\Loader\ArrayLoader;

$builder->setDefinition(StorageInterface::class, DatabaseStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition('widget.templating.filesystem.loader', ArrayLoader::class, [
    'factory' => function (WidgetRegistryInterface $registry, AdvancedFilesystemLoader $advancedFilesystemLoader) {
        $loader = new ArrayLoader();

        foreach ($registry->all() as $widget) {
            $name = str_replace('.', '/', $widget->getId());
            $advancedFilesystemLoader->setPath("@widget/{$name}", $widget->getViewsDirectory());
        }

        return $loader;
    },
    'arguments' => [
        service(WidgetRegistryInterface::class),
        service(AdvancedFilesystemLoader::class),
    ],
    'tags' => [ tag('twig.loader') ],
]);

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(HydratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(RendererInterface::class, Renderer::class, [
    'arguments' => [
        service(StorageInterface::class),
        service(WidgetRegistryInterface::class),
        service(EngineInterface::class),
    ],
]);

$builder->setDefinition(MenuWidget::class, MenuWidget::class, [
    'arguments' => [
        service(BuilderInterface::class),
    ],
    'tags' => [ tag('widget') ],
]);

$builder->setDefinition(TextWidget::class, TextWidget::class, [
    'tags' => [ tag('widget') ],
]);

$builder->setDefinition(FeaturesWidget::class, FeaturesWidget::class, [
    'tags' => [ tag('widget') ],
]);

$builder->setDefinition(MenuForm::class, MenuForm::class, [
    'arguments' => [
        service(MenuFinderFactory::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(WidgetExtension::class, WidgetExtension::class, [
    'arguments' => [
        service(RendererInterface::class),
        service(StorageInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(DefaultFieldsExtension::class, DefaultFieldsExtension::class, [
    'arguments' => [
        service(ManagerInterface::class),
    ],
    'tags' => [ tag('form_extension') ],
]);

$builder->setDefinition(WidgetForm::class, WidgetForm::class, [
    'arguments' => [
        service(ManagerInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
        service(CurrentWebsiteInterface::class),
        DbalQuery::class,
    ],
]);

$builder->setDefinition(WidgetFactoryInterface::class, WidgetFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(WidgetFormManagerFactory::class, WidgetFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(WidgetStorage::class),
    ],
]);

$builder->setDefinition(WidgetStorage::class, WidgetStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(SearchProvider::class, SearchProvider::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [
        tag('search.provider', 1000),
    ],
]);

$builder->setDefinition(DatatableFinder::class, DatatableFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
        service(WidgetRegistryInterface::class),
        service(ManagerInterface::class),
    ],
]);

/*$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);*/

/*$builder->mergeParameter('templating.paths', [
    'backend/widget' => dirname(__DIR__) . '/views/backend',
    'widget-base' => dirname(__DIR__) . '/views/widget-base',
]);*/
/*$builder->mergeParameter('twig.loader.array.templates', [
    'widget' => "{% extends [ '@theme/widget.tpl', '@parent/widget.tpl', '@widget-base/widget.tpl' ] %}",
]);*/
