<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigContentBlockTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private ContentTypeRoutingStrategyRegistry $strategyRegistry;
    private TranslatorInterface $translator;
    private Configuration $configuration;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        ContentTypeRoutingStrategyRegistry $strategyRegistry,
        TranslatorInterface $translator,
        Configuration $configuration
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->strategyRegistry = $strategyRegistry;
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    public function editorView(ContentType $contentType, FormView $formView, array $viewContext): View
    {
        return new View('@backend/content_builder/layout/content_block_type/editor.tpl', [
            'contentType' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
            'context' => $viewContext,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors, bool $creationMode): View
    {
        return new View('@backend/content_builder/layout/content_block_type/builder.tpl', [
            'fieldTypes' => $this->getFieldTypes($contentType),
            'routingStrategies' => $this->getRoutingStrategies($contentType),
            'model' => $data,
            'errors' => $errors,
            'multilingual' => $this->configuration->isMultilingual($contentType),
            'creationMode' => $creationMode,
        ]);
    }

    private function getFieldTypes(string $contentType): array
    {
        $types = [];

        foreach ($this->fieldTypeMappingRegistry->allForContentType($contentType) as $type => $data) {
            $types[$type] = [
                'id' => $type,
                'label' => $data['label'],
                'configuration' => $data['configuration'],
                'constraints' => $data['constraints'],
            ];
        }

        return $types;
    }

    private function getRoutingStrategies(string $contentType): array
    {
        $strategies = [];

        foreach ($this->strategyRegistry->all() as $strategy) {
            if ($strategy->supports($contentType) === false) {
                continue;
            }

            $strategies[] = [
                'id' => $strategy->getId(),
                'label' => $this->translator->trans(sprintf('contentTypeStrategy_%s', $strategy->getId()), [], 'content_builder'),
            ];
        }

        return $strategies;
    }
}
