<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeTypeProviderProvider implements NodeTypeProviderInterface
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
        $nodeType->setIsRoutable($options['is_routable']);
        $nodeType->setIsHierarchical($options['is_hierarchical']);
        $nodeType->setRoutableTaxonomyField($options['routable_taxonomy_field']);
        $nodeType->setTranslationDomain($options['translation_domain']);

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldName, $fieldOptions));
        }

        return $nodeType;
    }

    protected function buildNodeField(string $name, array $options): Field
    {
        return new Field(
            $name,
            $options['type'],
            (string) $options['label'],
            $options['is_title'],
            $options['is_slug'],
            $options['multilingual'],
            $options['multiple'],
            $options['constraints'],
            $options['options'],
            $this->fieldTypeMappingRegistry->getTypeFlags($options['type'])
        );
    }
}
