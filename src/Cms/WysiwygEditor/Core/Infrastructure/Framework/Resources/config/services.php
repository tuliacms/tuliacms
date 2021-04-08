<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\WysiwygEditor\Core\Infrastructure\Framework\Twig\Extension\WysiwygEditorExtension;
use Tulia\Cms\WysiwygEditor\Core\Application\DefaultEditor;
use Tulia\Cms\WysiwygEditor\Core\Application\Registry;
use Tulia\Cms\WysiwygEditor\Core\Application\RegistryInterface;
use Tulia\Cms\WysiwygEditor\TuliaEditor\Domain\Editor;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Templating\EngineInterface;

$builder->setDefinition(RegistryInterface::class, Registry::class, [
    'factory' => function (iterable $editors, Options $options) {
        return new Registry($editors, $options->get('wysiwyg_editor'));
    },
    'arguments' => [
        tagged('wysiwyg_editor'),
        service(Options::class)
    ],
]);

$builder->setDefinition(DefaultEditor::class, DefaultEditor::class, [
    'tags' => [ tag('wysiwyg_editor') ],
]);

$builder->setDefinition(WysiwygEditorExtension::class, WysiwygEditorExtension::class, [
    'arguments' => [
        service(RegistryInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(Editor::class, Editor::class, [
    'arguments' => [
        '@' . EngineInterface::class,
    ],
    'tags' => [ tag('wysiwyg_editor') ],
]);

$builder->mergeParameter('templating.paths', [
    'backend/wysiwyg-editor/tulia-editor' => dirname(__DIR__, 5) . '/TuliaEditor/Infrastructure/Framework/Resources/views/tulia-editor',
]);
