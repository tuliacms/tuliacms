<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeValidationRequestManipulator
{
    public function cleanFromValidationData(array $data): array
    {
        return [
            'type' => $data['type'],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->cleanSectionsFromValidationData($data['layout']['sidebar']['sections'])
                ],
                'main' => [
                    'sections' => $this->cleanSectionsFromValidationData($data['layout']['main']['sections'])
                ],
            ],
        ];
    }

    public function joinErrorsWithData(array $data, array $errors): array
    {
        $data['layout']['sidebar']['sections'] = $this->joinSectionErrorsWithData(
            $data['layout']['sidebar']['sections'],
            $errors['layout']['sidebar']['sections'] ?? []
        );
        $data['layout']['main']['sections'] = $this->joinSectionErrorsWithData(
            $data['layout']['main']['sections'],
            $errors['layout']['main']['sections'] ?? []
        );

        return $data;
    }

    private function cleanSectionsFromValidationData(array $sections): array
    {
        $newSections = [];

        foreach ($sections as $sk => $section) {
            $newFields = [];

            foreach ($section['fields'] as $fk => $field) {
                $newConfiguration = [];
                $newConstratints = [];

                foreach ($field['configuration'] as $ck => $conf) {
                    $newConfiguration[$ck] = [
                        'id' => $conf['id'],
                        'value' => $conf['value'],
                    ];
                }

                foreach ($field['constraints'] as $ck => $constr) {
                    $newModificators = [];

                    foreach ($constr['modificators'] as $mk => $modificator) {
                        $newModificators[$mk] = [
                            'id' => $modificator['id'],
                            'value' => $modificator['value'],
                        ];
                    }

                    $newConstratints[$ck] = [
                        'id' => $constr['id'],
                        'enabled' => $constr['enabled'] ?? false,
                        'modificators' => $newModificators,
                    ];
                }

                $newFields[$fk] = [
                    'code' => $field['code']['value'],
                    'name' => $field['name']['value'],
                    'multilingual' => $field['multilingual']['value'],
                    'type' => $field['type']['value'],
                    'configuration' => $newConfiguration,
                    'constraints' => $newConstratints,
                ];
            }

            $newSections[$sk] = [
                'code' => $section['code'],
                'name' => $section['name']['value'],
                'fields' => $newFields,
            ];
        }

        return $newSections;
    }

    private function joinSectionErrorsWithData(array $sections, array $errors): array
    {
        foreach ($sections as $sk => $section) {
            $sections[$sk]['name'] = [
                'value' => $section['name'],
                'valid' => empty($errors[$sk]['name']),
                'message' => $errors[$sk]['name'][0] ?? null,
            ];

            foreach ($section['fields'] as $fk => $field) {
                $fieldHasErrors = false;
                $configuration = [];
                $constraints = [];

                foreach ($field['configuration'] as $ck => $config) {
                    $configuration[$ck] = $config;
                    $configuration[$ck]['valid'] = empty($errors[$sk]['fields'][$fk]['configuration'][$ck]['value']);
                    $configuration[$ck]['message'] = $errors[$sk]['fields'][$fk]['configuration'][$ck]['value'][0] ?? null;

                    if (! $configuration[$ck]['valid']) {
                        $fieldHasErrors = true;
                    }
                }

                foreach ($field['constraints'] as $ck => $constraint) {
                    $constraints[$ck] = $constraint;
                    $constraints[$ck]['valid'] = true;
                    $constraints[$ck]['message'] = null;

                    foreach ($constraint['modificators'] as $mk => $modificator) {
                        $constraints[$ck]['modificators'][$mk] = $modificator;
                        $constraints[$ck]['modificators'][$mk]['valid'] = true;
                        $constraints[$ck]['modificators'][$mk]['message'] = null;
                    }
                }

                if (
                    ! empty($errors[$sk]['fields'][$fk]['code'])
                    || ! empty($errors[$sk]['fields'][$fk]['name'])
                    || ! empty($errors[$sk]['fields'][$fk]['multilingual'])
                    || ! empty($errors[$sk]['fields'][$fk]['type'])
                ) {
                    $fieldHasErrors = true;
                }

                $sections[$sk]['fields'][$fk] = [
                    'metadata' => [
                        'has_errors' => $fieldHasErrors,
                    ],
                    'code' => [
                        'value' => $field['code'],
                        'valid' => empty($errors[$sk]['fields'][$fk]['code']),
                        'message' => $errors[$sk]['fields'][$fk]['code'][0] ?? null,
                    ],
                    'name' => [
                        'value' => $field['name'],
                        'valid' => empty($errors[$sk]['fields'][$fk]['name']),
                        'message' => $errors[$sk]['fields'][$fk]['name'][0] ?? null,
                    ],
                    'multilingual' => [
                        'value' => $field['multilingual'],
                        'valid' => empty($errors[$sk]['fields'][$fk]['multilingual']),
                        'message' => $errors[$sk]['fields'][$fk]['multilingual'][0] ?? null,
                    ],
                    'type' => [
                        'value' => $field['type'],
                        'valid' => empty($errors[$sk]['fields'][$fk]['type']),
                        'message' => $errors[$sk]['fields'][$fk]['type'][0] ?? null,
                    ],
                    'configuration' => $configuration,
                    'constraints' => $constraints,
                ];
            }
        }

        return $sections;
    }
}
