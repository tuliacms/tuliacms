<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayToWriteModelTransformer
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
     *                                     [
     *                                         code: string,
     *                                         value: mixed
     *                                     ]
     *                                 ],
     *                                 constraints: [
     *                                     [
     *                                         code: string,
     *                                         modificators: [
     *                                             [
     *                                                 modificator: string,
     *                                                 value: mixed
     *                                             ]
     *                                         ]
     *                                     ]
     *                                 ],
     *                                 children: [...]
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
        $layout = [
            'code' => $type['code'] . '_layout',
            'name' => $type['name'] . ' Layout',
            'sections' => [],
        ];
        $fieldsAggs = [];

        foreach ($type['field_groups'] as $group) {
            $layout['sections'][$group['section']]['groups'][] = [
                'code' => $group['code'],
                'name' => $group['name'],
                'fields' => array_map(function (array $field) {
                    return $field['code'];
                }, $group['fields']),
            ];

            $fieldsAggs = $group['fields'] + $fieldsAggs;
        }

        $transformed = [
            'id' => $type['id'],
            'code' => $type['code'],
            'type' => $type['type'],
            'name' => $type['name'],
            'icon' => $type['icon'] ?? '',
            'is_routable' => $type['is_routable'] ?? false,
            'is_hierarchical' => $type['is_hierarchical'] ?? false,
            'routing_strategy' => $type['routing_strategy'] ?? '',
            'fields' => $fieldsAggs,
            'layout' => $layout
        ];

        return ContentType::recreateFromArray($transformed);
    }
}
