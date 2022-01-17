<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeBuilderTrait;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeTypeProvider implements NodeTypeProviderInterface
{
    use LayoutTypeBuilderTrait;

    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    protected string $defaultController = '';
    protected string $defaultLayoutBuilder = '';

    public function setDefaultLayoutBuilder(string $defaultLayoutBuilder): void
    {
        $this->defaultLayoutBuilder = $defaultLayoutBuilder;
    }

    public function setDefaultController(string $defaultController): void
    {
        $this->defaultController = $defaultController;
    }

    public function setFieldTypeMappingRegistry(FieldTypeMappingRegistry $fieldTypeMappingRegistry): void
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    protected function buildNodeType(string $name, array $options, LayoutType $layoutType): NodeType
    {
        $nodeType = new NodeType($name, $layoutType, (bool) $options['internal']);
        $nodeType->setController($options['controller'] ?? $this->defaultController);
        $nodeType->setIcon($options['icon'] ?? 'fa fa-box');
        $nodeType->setName($options['name']);
        $nodeType->setIsHierarchical((bool) $options['is_hierarchical']);

        foreach ($options['fields'] as $fieldCode => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldCode, $fieldOptions));
        }

        /**
         * Those following options needs fields to be set, so first we add fields,
         * and then those options.
         */
        $nodeType->setIsRoutable((bool) $options['is_routable']);
        $nodeType->setRoutableTaxonomyField($options['routable_taxonomy_field']);

        return $nodeType;
    }

    protected function buildNodeField(string $code, array $options): Field
    {
        return new Field([
            'code' => $code,
            'type' => $options['type'],
            'name' => (string) $options['name'],
            'is_multilingual' => (bool) $options['is_multilingual'],
            'is_multiple' => (bool) $options['is_multiple'],
            'taxonomy' => $options['taxonomy'],
            'configuration' => $options['configuration'],
            'constraints' => $options['constraints'],
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($options['type']),
            'builder_options' => function () use ($options) {
                return [
                    'constraints' => $options['constraints'],
                ];
            }
        ]);
    }
}
