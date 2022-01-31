<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class ModelToFormDataTransformer
{
    public function transform(ContentType $contentType, LayoutType $layoutType): array
    {
        $data = [
            'type' => [
                'code' => $contentType->getCode(),
                'name' => $contentType->getName(),
                'icon' => $contentType->getIcon(),
                'isRoutable' => $contentType->isRoutable(),
                'isHierarchical' => $contentType->isHierarchical(),
                'routingStrategy' => $contentType->getRoutingStrategy(),
            ],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->transformGroups($contentType, $layoutType, 'sidebar'),
                ],
                'main' => [
                    'sections' => $this->transformGroups($contentType, $layoutType, 'main'),
                ],
            ],
        ];

        return $data;
    }

    private function transformGroups(ContentType $contentType, LayoutType $layoutType, string $sectionName): array
    {
        $groups = [];

        foreach ($layoutType->getSections() as $code => $section) {
            if ($section->getCode() !== $sectionName) {
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
                    'fields' => $this->transformFields($contentType, $group->getFields()),
                ];
            }
        }

        return $groups;
    }

    private function transformFields(ContentType $contentType, array $fieldsCodes): array
    {
        $fields = [];

        foreach ($fieldsCodes as $code) {
            // Prevent errors. If field not exists, just dont show it in form.
            // Saving form without field removes the field from storage.
            if ($contentType->hasField($code) === false) {
                continue;
            }

            $field = $contentType->getField($code);

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

            foreach ($modificatorsSource['modificators'] as $id => $value) {
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
