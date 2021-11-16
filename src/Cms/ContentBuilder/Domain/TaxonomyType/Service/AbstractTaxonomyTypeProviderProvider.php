<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractTaxonomyTypeProviderProvider implements TaxonomyTypeProviderInterface
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(FieldTypeMappingRegistry $fieldTypeMappingRegistry)
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    protected function buildNodeType(string $name, array $options): TaxonomyType
    {
        $nodeType = new TaxonomyType($name);
        $nodeType->setController($options['controller']);
        $nodeType->setIsRoutable($options['is_routable']);
        $nodeType->setIsHierarchical($options['is_hierarchical']);
        $nodeType->setTranslationDomain($options['translation_domain']);
        $nodeType->setRoutingStrategy($options['routing_strategy']);

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldName, $fieldOptions));
        }

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
            'constraints' => $options['constraints'],
            'options' => $options['options'],
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($options['type'])
        ]);
    }
}
