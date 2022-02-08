<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\AbstractContentTypeProvider;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeDatabaseProvider extends AbstractContentTypeProvider
{
    private ConnectionInterface $connection;
    private array $fieldsSource = [];
    private array $configurationsSource = [];
    private array $constraintsSource = [];
    private array $modificatorsSource = [];
    private array $layoutsSource = [];
    private array $groupsSource = [];
    private array $fieldsListSource = [];
    private array $layoutsTypes = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->getTypes() as $type) {
            $result[] = $this->buildFromArray($type);
        }

        return $result;
    }

    private function getTypes(): array
    {
        $types = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type');

        foreach ($types as $key => $type) {
            $layoutType = $this->getLayoutType($types[$key]['layout']);
            $fields = $this->getFields($type['id']);

            foreach ($layoutType['sections'] as $sectionName => $section) {
                foreach ($section['groups'] as $groupName => $group) {
                    $groupFields = [];

                    foreach ($group['fields'] as $fieldCode) {
                        if (isset($fields[$fieldCode])) {
                            $groupFields[$fieldCode] = $fields[$fieldCode];
                        }
                    }

                    $layoutType['sections'][$sectionName]['groups'][$groupName]['fields'] = $groupFields;
                }
            }

            $types[$key]['layout'] = $layoutType;
        }

        return $types;
    }

    private function getFields(string $contentTypeId): array
    {
        if ($this->fieldsSource === []) {
            $this->fieldsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_field');
        }

        $fields = [];

        foreach ($this->fieldsSource as $field) {
            if ($field['content_type_id'] !== $contentTypeId) {
                continue;
            }

            $fields[$field['code']] = $field;
            $fields[$field['code']]['fields'] = (array) json_decode((string) $field['fields'], true);
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

    public function getLayoutType(string $layoutCode): array
    {
        $this->prefetch();

        return $this->layoutsTypes[$layoutCode];
    }

    private function prefetch(): void
    {
        if ($this->layoutsSource === []) {
            $this->layoutsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout');
        }

        if ($this->layoutsTypes === []) {
            foreach ($this->layoutsSource as $type) {
                $type['sections']['main']['groups'] = $this->getGroups($type['code'], 'main');
                $type['sections']['sidebar']['groups'] = $this->getGroups($type['code'], 'sidebar');

                $this->layoutsTypes[$type['code']] = $type;
            }
        }
    }

    private function getGroups(string $layoutCode, string $section): array
    {
        if ($this->groupsSource === []) {
            $this->groupsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout_group ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->groupsSource as $group) {
            if ($group['section'] === $section && $group['layout_type'] === $layoutCode) {
                $result[$group['code']] = $group;
                $result[$group['code']]['fields'] = $this->getLayoutFields($group['id']);
                $result[$group['code']]['interior'] = 'default';
            }
        }

        return $result;
    }

    private function getLayoutFields(string $groupId): array
    {
        if ($this->fieldsListSource === []) {
            $this->fieldsListSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout_group_field ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->fieldsListSource as $field) {
            if ($field['group_id'] === $groupId) {
                $result[] = $field['code'];
            }
        }

        return $result;
    }
}
