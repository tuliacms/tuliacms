<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
trait ContentTypeDatabaseProviderTrait
{
    private ConnectionInterface $connection;
    private array $fieldsSource = [];
    private array $configurationsSource = [];
    private array $constraintsSource = [];
    private array $modificatorsSource = [];

    private function getTypes(string $type): array
    {
        $types = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type WHERE `type` = :type', ['type' => $type]);

        foreach ($types as $key => $type) {
            $types[$key]['layout'] = $type['code'] . '_layout';
            $types[$key]['fields'] = $this->getFields($type['code']);
        }

        return $types;
    }

    private function getFields(string $nodeType): array
    {
        if ($this->fieldsSource === []) {
            $this->fieldsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field');
        }

        $fields = [];

        foreach ($this->fieldsSource as $field) {
            if ($field['node_type'] !== $nodeType) {
                continue;
            }

            $fields[$field['code']] = $field;
            $fields[$field['code']]['configuration'] = $this->getConfiguration($field['id']);
            $fields[$field['code']]['constraints'] = $this->getConstraints($field['id']);
        }

        return $fields;
    }

    private function getConfiguration(string $fieldId): array
    {
        if ($this->configurationsSource === []) {
            $this->configurationsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_configuration');
        }

        $configs = [];

        foreach ($this->configurationsSource as $config) {
            if ($config['field_id'] !== $fieldId) {
                continue;
            }

            $configs[$config['code']] = $config['value'];
        }

        return $configs;
    }

    private function getConstraints(string $fieldId): array
    {
        if ($this->constraintsSource === []) {
            $this->constraintsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_constraint');
        }

        $configs = [];

        foreach ($this->constraintsSource as $constraint) {
            if ($constraint['field_id'] !== $fieldId) {
                continue;
            }

            $configs[$constraint['code']]['modificators'] = $this->getConstraintModificators($constraint['id']);
        }

        return $configs;
    }

    private function getConstraintModificators(string $constraintId): array
    {
        if ($this->modificatorsSource === []) {
            $this->modificatorsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field_constraint_modificator');
        }

        $modificators = [];

        foreach ($this->modificatorsSource as $modificator) {
            if ($modificator['constraint_id'] !== $constraintId) {
                continue;
            }

            $modificators[$modificator['modificator']] = $modificator['value'];
        }

        return $modificators;
    }
}
