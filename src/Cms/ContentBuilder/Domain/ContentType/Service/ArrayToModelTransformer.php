<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\Section;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToModelTransformer
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private Configuration $config;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        Configuration $config
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->config = $config;
    }

    public function transform(array $type): ContentType
    {
        return $this->buildContentType($type, $this->buildLayoutType($type));
    }

    protected function buildLayoutType(array $type): LayoutType
    {
        $layoutType = new LayoutType($type['code'] . '_layout');
        $layoutType->setName($type['name'] . ' Layout');
        $layoutType->setBuilder($type['builder'] ?? $this->config->getLayoutBuilder($type['type']));

        $sections = [
            'main' => [],
            'sidebar' => [],
        ];

        foreach ($type['field_groups'] as $group) {
            $sections[$group['section']][] = $group;
        }

        foreach ($sections as $section => $groups) {
            $layoutType->addSection(new Section($section, $this->buildFieldsGroups($groups)));
        }

        return $layoutType;
    }

    private function buildFieldsGroups(array $groups): array
    {
        $result = [];

        foreach ($groups as $group) {
            $result[$group['code']] = new FieldsGroup(
                $group['code'],
                $group['name'],
                (bool) ($group['active'] ?? 0),
                (string) ($group['interior'] ?? ''),
                array_map(static function (array $field) {
                    return $field['code'];
                }, $group['fields'])
            );
        }

        return $result;
    }

    protected function buildContentType(array $type, LayoutType $layoutType): ContentType
    {
        $nodeType = new ContentType($type['id'], $type['code'], $type['type'], $layoutType, (bool) ($type['internal'] ?? false));
        $nodeType->setController($type['controller'] ?? $this->config->getController($type['type']));
        $nodeType->setIcon($type['icon'] ?? 'fa fa-box');
        $nodeType->setName($type['name'] ?? '');
        $nodeType->setIsHierarchical((bool) ($type['is_hierarchical'] ?? false));
        $nodeType->setRoutingStrategy($type['routing_strategy'] ?? '');

        foreach ($type['field_groups'] ?? [] as $group) {
            foreach ($group['fields'] ?? [] as $field) {
                $nodeType->addField($this->buildNodeField($field));
            }
        }

        /**
         * Those following type needs fields to be set, so first we add fields,
         * and then those type.
         */
        $nodeType->setIsRoutable((bool) ($type['is_routable'] ?? false));

        return $nodeType;
    }

    protected function buildNodeField(array $field): Field
    {
        return new Field([
            'code' => $field['code'],
            'type' => $field['type'],
            'name' => (string) $field['name'],
            'is_multilingual' => (bool) ($field['is_multilingual'] ?? false),
            'is_multiple' => (bool) ($field['is_multiple'] ?? false),
            'taxonomy' => $field['taxonomy'] ?? null,
            'configuration' => $field['configuration'] ?? [],
            'constraints' => $field['constraints'] ?? [],
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($field['type']),
            'builder_options' => function () use ($field) {
                return [
                    'constraints' => $field['constraints'],
                ];
            }
        ]);
    }
}
