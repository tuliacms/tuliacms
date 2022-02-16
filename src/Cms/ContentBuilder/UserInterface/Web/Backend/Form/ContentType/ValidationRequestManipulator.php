<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ValidationRequestManipulator
{
    public function cleanFromValidationData(array $data): array
    {
        return [
            'type' => $data['type'],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->cleanSectionsFromValidationData($data['layout']['sidebar']['sections'] ?? [])
                ],
                'main' => [
                    'sections' => $this->cleanSectionsFromValidationData($data['layout']['main']['sections'] ?? [])
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
            $newSections[$sk] = [
                'code' => $section['code'],
                'name' => $section['name']['value'],
                'fields' => $this->cleanFromFieldsCollection($section['fields']),
            ];
        }

        return $newSections;
    }

    private function cleanFromFieldsCollection(array $fields): array
    {
        $newFields = [];

        foreach ($fields as $fk => $field) {
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
                'children' => $this->cleanFromFieldsCollection($field['children']),
            ];
        }

        return $newFields;
    }

    private function joinSectionErrorsWithData(array $sections, array $errors): array
    {
        foreach ($sections as $sk => $section) {
            $sections[$sk]['name'] = [
                'value' => $section['name'],
                'valid' => empty($errors[$sk]['name']),
                'message' => $errors[$sk]['name'][0] ?? null,
            ];

            $sections[$sk]['fields'] = $this->joinFieldsErrorsWithData($section['fields'], $errors[$sk]['fields'] ?? []);
        }

        return $sections;
    }

    private function joinFieldsErrorsWithData(array $fields, array $errors): array
    {
        foreach ($fields as $fk => $field) {
            $fieldHasErrors = false;
            $configuration = [];
            $constraints = [];

            foreach ($field['configuration'] as $ck => $config) {
                $configuration[$ck] = $config;
                $configuration[$ck]['valid'] = empty($errors[$fk]['configuration'][$ck]['value']);
                $configuration[$ck]['message'] = $errors[$fk]['configuration'][$ck]['value'][0] ?? null;

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
                ! empty($errors[$fk]['code'])
                || ! empty($errors[$fk]['name'])
                || ! empty($errors[$fk]['multilingual'])
                || ! empty($errors[$fk]['type'])
            ) {
                $fieldHasErrors = true;
            }

            $fields[$fk] = [
                'metadata' => [
                    'has_errors' => $fieldHasErrors,
                ],
                'code' => [
                    'value' => $field['code'],
                    'valid' => empty($errors[$fk]['code']),
                    'message' => $errors[$fk]['code'][0] ?? null,
                ],
                'name' => [
                    'value' => $field['name'],
                    'valid' => empty($errors[$fk]['name']),
                    'message' => $errors[$fk]['name'][0] ?? null,
                ],
                'multilingual' => [
                    'value' => $field['multilingual'],
                    'valid' => empty($errors[$fk]['multilingual']),
                    'message' => $errors[$fk]['multilingual'][0] ?? null,
                ],
                'type' => [
                    'value' => $field['type'],
                    'valid' => empty($errors[$fk]['type']),
                    'message' => $errors[$fk]['type'][0] ?? null,
                ],
                'configuration' => $configuration,
                'constraints' => $constraints,
                'children' => $this->joinFieldsErrorsWithData($field['children'], $errors[$fk]['children'] ?? []),
            ];
        }

        return $fields;
    }
}
