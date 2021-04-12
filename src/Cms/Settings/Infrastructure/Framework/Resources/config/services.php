<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\Platform\Infrastructure\Utilities\DateTime\DateFormatterInterface;
use Tulia\Cms\Settings\CmsSettingsGroup;
use Tulia\Cms\Settings\Infrastructure\Cms\BackendMenu\SettingsMenuBuilder;
use Tulia\Cms\Settings\Infrastructure\Cms\SearchAnything\SearchProvider;
use Tulia\Cms\Settings\Registry;
use Tulia\Cms\Settings\RegistryInterface;
use Tulia\Cms\Settings\UI\Web\Form\SettingsForm;
use Tulia\Cms\WysiwygEditor\Core\Application\RegistryInterface as WysiwygEditorRegistry;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'arguments' => [
        tagged('settings.group_factory'),
        tagged('settings.group'),
    ],
]);

$builder->setDefinition(SearchProvider::class, SearchProvider::class, [
    'arguments' => [
        service(RegistryInterface::class),
        service(FormFactoryInterface::class),
        service(Options::class),
        service(TranslatorInterface::class),
        service(RouterInterface::class),
    ],
    'tags' => [
        tag('search.provider', 600),
    ],
]);

$builder->setDefinition(CmsSettingsGroup::class, CmsSettingsGroup::class, [
    'tags' => [
        tag('settings.group', 1000),
    ],
]);

$builder->setDefinition(SettingsMenuBuilder::class, SettingsMenuBuilder::class, [
    'arguments' => [
        service(BuilderHelperInterface::class),
    ],
    'tags' => [ tag('backend_menu.builder') ],
]);

$builder->setDefinition(SettingsForm::class, SettingsForm::class, [
    'arguments' => [
        service(DateFormatterInterface::class),
        service(WysiwygEditorRegistry::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/settings' => dirname(__DIR__) . '/views/frontend',
    'backend/settings' => dirname(__DIR__) . '/views/backend',
]);

$builder->mergeParameter('settings.providers', [
    dirname(__DIR__) . '/config/settings.php',
]);
