<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeTypeProvider implements NodeTypeProviderInterface
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(FieldTypeMappingRegistry $fieldTypeMappingRegistry)
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    protected function buildNodeType(string $name, array $options): NodeType
    {
        $nodeType = new NodeType($name, $options['layout']);
        $nodeType->setController($options['controller']);
        $nodeType->setIcon($options['icon']);
        $nodeType->setName($options['name']);
        $nodeType->setIsHierarchical($options['is_hierarchical']);

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldName, $fieldOptions));
        }

        /**
         * Those following options needs fields to be set, so first we add fields,
         * and then those options.
         */
        $nodeType->setIsRoutable($options['is_routable']);
        $nodeType->setRoutableTaxonomyField($options['routable_taxonomy_field']);

        return $nodeType;
    }

    protected function buildNodeField(string $name, array $options): Field
    {
        return new Field([
            'name' => $name,
            'type' => $options['type'],
            'label' => (string) $options['label'],
            'multilingual' => $options['multilingual'],
            'multiple' => $options['multiple'],
            'taxonomy' => $options['taxonomy'],
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($options['type']),
            'builder_options' => function () use ($options) {
                return [
                    'constraints' => $options['constraints'],
                ];
            }
        ]);
    }
}
