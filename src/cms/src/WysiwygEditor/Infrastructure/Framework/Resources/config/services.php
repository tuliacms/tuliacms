<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Twig\Extension\WysiwygEditorExtension;
use Tulia\Cms\WysiwygEditor\Application\DefaultEditor;
use Tulia\Cms\WysiwygEditor\Application\Registry;
use Tulia\Cms\WysiwygEditor\Application\RegistryInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

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
