<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Transformer;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeModelToFormDataTransformer
{
    public function transform(NodeType $nodeType, LayoutType $layoutType): array
    {
        $data = [
            'type' => [
                'code' => $nodeType->getCode(),
                'name' => $nodeType->getName(),
                'icon' => $nodeType->getIcon(),
                'isRoutable' => $nodeType->isRoutable(),
                'isHierarchical' => $nodeType->isHierarchical(),
                'taxonomyField' => $nodeType->getRoutableTaxonomyField(),
            ],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->transformGroups($nodeType, $layoutType, 'sidebar'),
                ],
                'main' => [
                    'sections' => $this->transformGroups($nodeType, $layoutType, 'main'),
                ],
            ],
        ];

        //dump($nodeType, $layoutType, $data);exit;

        return $data;
    }

    private function transformGroups(NodeType $nodeType, LayoutType $layoutType, string $sectionName): array
    {
        $groups = [];

        foreach ($layoutType->getSections() as $code => $section) {
            if ($section->getName() !== $sectionName) {
                continue;
            }

            foreach ($section->getFieldsGroups() as $group) {
                $groups[] = [
                    'code' => $group->getCode(),
                    'name' => [
                        'value' => $group->getName(),
                        'valid' => true,
                        'message' => null,
                    ],
                    'fields' => $this->transformFields($nodeType, $group->getFields()),
                ];
            }
        }

        return $groups;
    }

    private function transformFields(NodeType $nodeType, array $fieldsCodes): array
    {
        $fields = [];

        foreach ($fieldsCodes as $code) {
            // Prevent errors. If field not exists, just dont show it in form.
            // Saving form without field removes the field from storage.
            if ($nodeType->hasField($code) === false) {
                continue;
            }

            $field = $nodeType->getField($code);

            $fields[] = [
                'metadata' => [
                    'has_errors' => false,
                ],
                'code' => [
                    'value' => $field->getCode(),
                    'valid' => true,
                    'message' => null,
                ],
                'name' => [
                    'value' => $field->getName(),
                    'valid' => true,
                    'message' => null,
                ],
                'multilingual' => [
                    'value' => $field->isMultilingual(),
                    'valid' => true,
                    'message' => null,
                ],
                'type' => [
                    'value' => $field->getType(),
                    'valid' => true,
                    'message' => null,
                ],
                'configuration' => $this->transformFieldConfiguration($field->getConfiguration()),
                'constraints' => $this->transformFieldConstraints($field->getConstraints()),
            ];
        }

        return $fields;
    }

    private function transformFieldConfiguration(array $configuration): array
    {
        $result = [];

        foreach ($configuration as $name => $value) {
            $result[] = [
                'id' => $name,
                'value' => $value,
                'valid' => true,
                'message' => null,
            ];
        }

        return $result;
    }

    private function transformFieldConstraints(array $constraints): array
    {
        $result = [];

        foreach ($constraints as $name => $modificatorsSource) {
            $modificators = [];

            foreach ($modificatorsSource as $id => $value) {
                $modificators[] = [
                    'id' => $id,
                    'value' => $value,
                    'valid' => true,
                    'message' => null,
                ];
            }

            $result[] = [
                'id' => $name,
                'enabled' => true,
                'valid' => true,
                'message' => null,
                'modificators' => $modificators,
            ];
        }

        return $result;
    }
}
