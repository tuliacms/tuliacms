<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeBuilderTrait;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractContentTypeProvider implements ContentTypeProviderInterface
{
    use LayoutTypeBuilderTrait;

    protected FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    protected Configuration $config;

    public function setFieldTypeMappingRegistry(FieldTypeMappingRegistry $fieldTypeMappingRegistry): void
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    public function setConfiguration(Configuration $config): void
    {
        $this->config = $config;
    }

    protected function buildContentType(string $id, string $name, array $options, LayoutType $layoutType): ContentType
    {
        $nodeType = new ContentType($id, $name, $options['type'], $layoutType, (bool) $options['internal']);
        $nodeType->setController($options['controller'] ?? $this->config->getController($options['type']));
        $nodeType->setIcon($options['icon'] ?? 'fa fa-box');
        $nodeType->setName($options['name']);
        $nodeType->setIsHierarchical((bool) $options['is_hierarchical']);
        $nodeType->setRoutingStrategy($options['routing_strategy']);

        foreach ($options['fields'] as $fieldCode => $fieldOptions) {
            $nodeType->addField($this->buildNodeField($fieldCode, $fieldOptions));
        }

        /**
         * Those following options needs fields to be set, so first we add fields,
         * and then those options.
         */
        $nodeType->setIsRoutable((bool) $options['is_routable']);

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
