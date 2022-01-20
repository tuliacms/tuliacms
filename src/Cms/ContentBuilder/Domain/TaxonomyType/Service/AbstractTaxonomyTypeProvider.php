<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeBuilderTrait;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractTaxonomyTypeProvider implements TaxonomyTypeProviderInterface
{
    use LayoutTypeBuilderTrait;

    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    protected string $defaultLayoutBuilder = '';
    protected string $defaultController = '';

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

    protected function buildTaxonomyType(string $code, array $options, LayoutType $layoutType): TaxonomyType
    {
        $taxonomyType = new TaxonomyType($code, 'taxonomy', $layoutType, (bool) $options['internal']);
        $taxonomyType->setController($options['controller'] ?? $this->defaultController);
        $taxonomyType->setIsRoutable((bool) $options['is_routable']);
        $taxonomyType->setName($options['name']);
        $taxonomyType->setIsHierarchical((bool) $options['is_hierarchical']);
        $taxonomyType->setRoutingStrategy($options['routing_strategy']);

        foreach ($options['fields'] as $fieldName => $fieldOptions) {
            $taxonomyType->addField($this->buildNodeField($fieldName, $fieldOptions));
        }

        return $taxonomyType;
    }

    protected function buildNodeField(string $name, array $options): Field
    {
        return new Field([
            'name' => $name,
            'type' => $options['type'],
            'label' => (string) $options['label'],
            'multilingual' => $options['multilingual'],
            'multiple' => $options['multiple'],
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($options['type']),
            'builder_options' => function () use ($options) {
                return array_merge($options['options'], ['constraints' => $options['constraints']]);
            }
        ]);
    }
}
