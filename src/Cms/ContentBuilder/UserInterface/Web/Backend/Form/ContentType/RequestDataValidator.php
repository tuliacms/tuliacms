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
        foreach ($this->requestData['layout'] as $sectionCode => $section) {
            // Cleaning data must be started from top to bottom. To first remove example invalid field type,
            // before checking the modificators for constraints.
            $this->requestData['layout'][$sectionCode]['sections'] = $this->removeInvalidGroups($this->requestData['layout'][$sectionCode]['sections']);

            foreach ($section['sections'] as $groupCode => $group) {
                $this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields'] = $this->removeNotExistentFieldTypes($this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields']);
                $this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields'] = $this->removeNotExistentConfigurationEntries($this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields']);
                $this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields'] = $this->removeNotExistentConstraintModificators($this->requestData['layout'][$sectionCode]['sections'][$groupCode]['fields']);
            }
        }

        return $this->requestData;
    }

    public function getCleaningResult(): array
    {
        return $this->cleaningResult;
    }

    private function removeInvalidGroups(array $groups): array
    {
        foreach ($groups as $key => $group) {
            if ($this->codenameValidator->isCodenameValid($group['code']) === false) {
                unset($groups[$key]);
                $this->log('Fields group {group_name} removed, cause: section Code must contain only lowercased alphanums and underlines.', [
                    'group_name' => $group['label'],
                ]);
                continue;
            }
        }

        return $groups;
    }

    private function removeNotExistentFieldTypes(array $fields): array
    {
        foreach ($fields as $fieldKey => $field) {
            if ($this->fieldTypeMappingRegistry->hasType($field['type']) === false) {
                unset($fields[$fieldKey]);
                $this->log('Field {field_label} removed, cause: field type {field_type} not exists.', [
                    'field_type' => $field['type'],
                    'field_label' => $field['label'],
                ]);
                continue;
            }
        }

        return $fields;
    }

    private function removeNotExistentConfigurationEntries(array $fields): array
    {
        foreach ($fields as $fieldKey => $field) {
            $type = $this->fieldTypeMappingRegistry->get($field['type']);

            foreach ($field['configuration'] as $configurationKey => $configuration) {
                if (isset($type['configuration'][$configuration['id']]) === false) {
                    unset($fields['fields'][$fieldKey]['configuration'][$configurationKey]);
                    $this->log('Configuration key {configuration_key} removed, cause: configuration key for {field_type} field not exists.', [
                        'configuration_key' => $configuration['id'],
                        'field_type' => $field['type'],
                    ]);
                }
            }
        }

        return $fields;
    }

    private function removeNotExistentConstraintModificators(array $fields): array
    {
        foreach ($fields as $fieldKey => $field) {
            $type = $this->fieldTypeMappingRegistry->get($field['type']);

            foreach ($field['constraints'] as $constraintKey => $constraint) {
                if (isset($type['constraints'][$constraint['id']]) === false) {
                    unset($fields[$fieldKey]['constraints'][$constraintKey]);
                    $this->log('Constraint {constraint_id} removed, cause: constraint not exists in {field_type} field type.', [
                        'constraint_id' => $constraint['id'],
                        'field_type' => $field['type'],
                    ]);
                }
            }
        }

        return $fields;
    }

    protected function log(string $message, array $context = []): void
    {
        $this->cleaningResult[] = [
            'message' => $message,
            'context' => $context,
        ];
    }
}
