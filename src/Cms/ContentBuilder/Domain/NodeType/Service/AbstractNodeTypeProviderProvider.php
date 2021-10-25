<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeTypeProviderProvider implements NodeTypeProviderInterface
{
    protected function buildNodeType(string $name, array $options): NodeType
    {
        $nodeType = new NodeType($name, $options['translation_domain']);
        $nodeType->setController($options['controller']);
        $nodeType->setIsRoutable($options['is_routable']);
        $nodeType->setIsHierarchical($options['is_hierarchical']);
        $nodeType->setLayout($options['layout']);
        $nodeType->setRoutableTaxonomyField($options['routable_taxonomy_field']);

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldName, $fieldOptions));
        }

        return $nodeType;
    }

    protected function buildNodeField(string $name, array $options): Field
    {
        $field = new Field($name, $options['type'], $options['options']);
        $field->setLabel($options['label']);
        $field->setIsTitle($options['is_title']);
        $field->setIsSlug($options['is_slug']);

        return $field;
    }
}
