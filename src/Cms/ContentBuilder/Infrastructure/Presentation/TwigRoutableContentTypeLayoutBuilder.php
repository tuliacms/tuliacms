<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Presentation;

use Symfony\Component\Form\FormView;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRoutableContentTypeLayoutBuilder implements LayoutTypeBuilderInterface
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private ContentTypeRoutingStrategyRegistry $strategyRegistry;
    private TranslatorInterface $translator;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        ContentTypeRoutingStrategyRegistry $strategyRegistry,
        TranslatorInterface $translator
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->strategyRegistry = $strategyRegistry;
        $this->translator = $translator;
    }

    public function editorView(ContentType $contentType, FormView $formView): View
    {
        return new View('@backend/content_builder/layout/routable_content_type/editor.tpl', [
            'type' => $contentType,
            'layout' => $contentType->getLayout(),
            'form' => $formView,
        ]);
    }

    public function builderView(string $contentType, array $data, array $errors): View
    {
        return new View('@backend/content_builder/layout/routable_content_type/builder.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'routingStrategies' => $this->getRoutingStrategies($contentType),
            'model' => $data,
            'errors' => $errors,
        ]);
    }

    private function getFieldTypes(): array
    {
        $types = [];

        foreach ($this->fieldTypeMappingRegistry->all() as $type => $data) {
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
