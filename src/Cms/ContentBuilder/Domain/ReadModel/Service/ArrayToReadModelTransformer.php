<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Section;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
// @todo Move Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry to Domain layer

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToReadModelTransformer
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

    /**
     * Array structure of $type argument:
     * [
     *     id: null|string,
     *     code: string,
     *     type: string,
     *     name: string,
     *     icon: null|string,
     *     controller: null|string,
     *     is_routable: null|bool,
     *     is_hierarchical: null|bool,
     *     routing_strategy: null|string,
     *     layout: [
     *         sections: [
     *             {section_code}: [...],
     *             {section_code}: [
     *                 groups: [
     *                     {group_code}: [...],
     *                     {group_code}: [
     *                         name: string,
     *                         active: bool,
     *                         fields: [
     *                             {field_code}: [...],
     *                             {field_code}: [
     *                                 type: string,
     *                                 name: string,
     *                                 is_multilingual: null|bool,
     *                                 configuration: [
     *                                     {code}: {value} mixed,
     *                                     ...
     *                                 ],
     *                                 constraints: [
     *                                     {constraint_name}: [
     *                                         modificators: [
     *                                             {code}: {value} mixed,
     *                                             ...
     *                                         ]
     *                                     ]
     *                                 ]
     *                             ]
     *                         ]
     *                     ]
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     */
    public function transform(array $type): ContentType
    {
        return $this->buildContentType($type);
    }

    protected function buildContentType(array $type): ContentType
    {
        $layoutType = $this->buildLayoutType($type);
        $contentType = new ContentType($type['id'] ?? null, $type['code'], $type['type'], $layoutType, (bool) ($type['internal'] ?? false));
        $contentType->setController($type['controller'] ?? $this->config->getController($type['type']));
        $contentType->setIcon($type['icon'] ?? 'fa fa-box');
        $contentType->setName($type['name']);
        $contentType->setIsHierarchical((bool) $type['is_hierarchical']);
        $contentType->setRoutingStrategy($type['routing_strategy']);

        $fields = [];

        foreach ($type['layout']['sections'] as $sectionCode => $section) {
            foreach ($section['groups'] as $groupCode => $group) {
                foreach ($group['fields'] as $fieldCode => $field) {
                    $fields[$fieldCode] = $field;
                }
            }
        }

        $fields = $this->findFieldsParents($fields);

        foreach ($fields as $fieldCode => $field) {
            $contentType->addField($this->buildNodeField($fieldCode, $field));
        }

        /**
         * Those following type needs fields to be set, so first we add fields,
         * and then those type.
         */
        $contentType->setIsRoutable((bool) $type['is_routable']);

        return $contentType;
    }

    protected function buildNodeField(string $code, array $type): Field
    {
        return new Field([
            'code' => $code,
            'type' => $type['type'],
            'name' => (string) $type['name'],
            'is_multilingual' => (bool) $type['is_multilingual'],
            'taxonomy' => $type['taxonomy'] ?? '',
            'configuration' => $type['configuration'],
            'constraints' => $type['constraints'],
            'parent' => $type['parent'] ?? null,
            'flags' => $this->fieldTypeMappingRegistry->getTypeFlags($type['type']),
            'builder_type' => function () use ($type) {
                return [
                    'constraints' => $type['constraints'],
                ];
            }
        ]);
    }

    protected function buildLayoutType(array $type): LayoutType
    {
        $layoutType = new LayoutType($type['code'] . '_layout');
        $layoutType->setName($type['name'] . ' Layout');
        $layoutType->setBuilder($type['builder'] ?? $this->config->getLayoutBuilder($type['type']));

        foreach ($type['layout']['sections'] as $sectionCode => $section) {
            $sectionObject = new Section($sectionCode);

            foreach ($section['groups'] as $groupCode => $group) {
                $sectionObject->addFieldsGroup(new FieldsGroup($groupCode, $group['name'], (bool) $group['active'], '', array_keys($group['fields'])));
            }

            $layoutType->addSection($sectionObject);
        }

        return $layoutType;
    }

    private function findFieldsParents(array $fields): array
    {
        foreach ($fields as $code => $field) {
            foreach ($field['fields'] as $item) {
                $fields[$item]['parent'] = $code;
            }
        }

        return $fields;
    }
}
