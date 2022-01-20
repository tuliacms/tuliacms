<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class RequestDataValidator
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private CodenameValidator $codenameValidator;
    private array $requestData;
    private array $cleaningResult = [];

    public function __construct(array $requestData, FieldTypeMappingRegistry $fieldTypeMappingRegistry, CodenameValidator $codenameValidator)
    {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->requestData = $requestData;
        $this->codenameValidator = $codenameValidator;
    }

    public function cleanForInvalidElements(): array
    {
        // Cleaning data must be started from top to bottom. To first remove example invalid field type,
        // before checking the modificators for constraints.
        $this->requestData['layout']['sidebar']['sections'] = $this->removeInvalidSections($this->requestData['layout']['sidebar']['sections']);
        $this->requestData['layout']['main']['sections'] = $this->removeInvalidSections($this->requestData['layout']['main']['sections']);

        $this->requestData['layout']['sidebar']['sections'] = $this->removeNotExistentFieldTypes($this->requestData['layout']['sidebar']['sections']);
        $this->requestData['layout']['main']['sections'] = $this->removeNotExistentFieldTypes($this->requestData['layout']['main']['sections']);

        $this->requestData['layout']['sidebar']['sections'] = $this->removeNotExistentConfigurationEntries($this->requestData['layout']['sidebar']['sections']);
        $this->requestData['layout']['main']['sections'] = $this->removeNotExistentConfigurationEntries($this->requestData['layout']['main']['sections']);

        $this->requestData['layout']['sidebar']['sections'] = $this->removeNotExistentConstraintModificators($this->requestData['layout']['sidebar']['sections']);
        $this->requestData['layout']['main']['sections'] = $this->removeNotExistentConstraintModificators($this->requestData['layout']['main']['sections']);

        return $this->requestData;
    }

    public function getCleaningResult(): array
    {
        return $this->cleaningResult;
    }

    private function removeInvalidSections(array $sections): array
    {
        foreach ($sections as $sectionKey => $section) {
            if ($this->codenameValidator->isCodenameValid($section['code']) === false) {
                unset($sections[$sectionKey]);
                $this->log('Section {section_name} removed, cause: section Code must contain only lowercased alphanums and underlines.', [
                    'section_name' => $section['label'],
                ]);
                continue;
            }
        }

        return $sections;
    }

    private function removeNotExistentFieldTypes(array $sections): array
    {
        foreach ($sections as $sectionKey => $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                if ($this->fieldTypeMappingRegistry->hasType($field['type']) === false) {
                    unset($sections[$sectionKey]['fields'][$fieldKey]);
                    $this->log('Field {field_label} removed, cause: field type {field_type} not exists.', [
                        'field_type' => $field['type'],
                        'field_label' => $field['label'],
                    ]);
                    continue;
                }
            }
        }

        return $sections;
    }

    private function removeNotExistentConfigurationEntries(array $sections): array
    {
        foreach ($sections as $sectionKey => $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                $type = $this->fieldTypeMappingRegistry->get($field['type']);

                foreach ($field['configuration'] as $configurationKey => $configuration) {
                    if (isset($type['configuration'][$configuration['id']]) === false) {
                        unset($sections[$sectionKey]['fields'][$fieldKey]['configuration'][$configurationKey]);
                        $this->log('Configuration key {configuration_key} removed, cause: configuration key for {field_type} field not exists.', [
                            'configuration_key' => $configuration['id'],
                            'field_type' => $field['type'],
                        ]);
                    }
                }
            }
        }

        return $sections;
    }

    private function removeNotExistentConstraintModificators(array $sections): array
    {
        foreach ($sections as $sectionKey => $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                $type = $this->fieldTypeMappingRegistry->get($field['type']);

                foreach ($field['constraints'] as $constraintKey => $constraint) {
                    if (isset($type['constraints'][$constraint['id']]) === false) {
                        unset($sections[$sectionKey]['fields'][$fieldKey]['constraints'][$constraintKey]);
                        $this->log('Constraint {constraint_id} removed, cause: constraint not exists in {field_type} field type.', [
                            'constraint_id' => $constraint['id'],
                            'field_type' => $field['type'],
                        ]);
                    }
                }
            }
        }

        return $sections;
    }

    protected function log(string $message, array $context = []): void
    {
        $this->cleaningResult[] = [
            'message' => $message,
            'context' => $context,
        ];
    }
}
