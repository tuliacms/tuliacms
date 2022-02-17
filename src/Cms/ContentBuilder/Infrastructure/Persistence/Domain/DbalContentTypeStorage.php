<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\Domain;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeStorageInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalContentTypeStorage implements ContentTypeStorageInterface
{
    private ConnectionInterface $connection;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        ConnectionInterface $connection,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->connection = $connection;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function find(string $id): ?array
    {
        $type = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type WHERE id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($type === []) {
            return null;
        }

        return $this->collectDetailsForType($type[0]);
    }

    public function findByCode(string $code): ?array
    {
        $type = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type WHERE code = :code LIMIT 1',
            ['code' => $code]
        );

        if ($type === []) {
            return null;
        }

        return $this->collectDetailsForType($type[0]);
    }

    public function insert(array $contentType): void
    {
        $this->connection->insert('#__content_type', [
            'id' => $contentType['id'],
            'code' => $contentType['code'],
            'type' => $contentType['type'],
            'name' => $contentType['name'],
            'icon' => $contentType['icon'],
            'controller' => $contentType['controller'],
            'is_routable' => $contentType['is_routable'] ? '1' : '0',
            'is_hierarchical' => $contentType['is_hierarchical'] ? '1' : '0',
            'routing_strategy' => $contentType['routing_strategy'],
            'layout' => $contentType['layout']['code'],
        ]);

        foreach ($contentType['fields'] as $field) {
            $fieldId = $this->uuidGenerator->generate();

            $this->connection->insert('#__content_type_field', [
                'id' => $fieldId,
                'code' => $field['code'],
                'content_type_id' => $contentType['id'],
                'type' => $field['type'],
                'name' => $field['name'],
                'parent' => $field['parent'],
                'is_multilingual' => $field['is_multilingual'] ? '1' : '0',
            ]);

            foreach ($field['configuration'] as $code => $value) {
                $this->connection->insert('#__content_type_field_configuration', [
                    'field_id' => $fieldId,
                    'code' => $code,
                    'value' => $value,
                ]);
            }

            foreach ($field['constraints'] as $constraint) {
                $constraintId = $this->uuidGenerator->generate();

                $this->connection->insert('#__content_type_field_constraint', [
                    'id' => $constraintId,
                    'field_id' => $fieldId,
                    'code' => $constraint['code'],
                ]);

                foreach ($constraint['modificators'] as $modificator => $value) {
                    $this->connection->insert('#__content_type_field_constraint_modificator', [
                        'constraint_id' => $constraintId,
                        'modificator' => $modificator,
                        'value' => $value,
                    ]);
                }
            }
        }

        $this->connection->insert('#__content_type_layout', [
            'code' => $contentType['layout']['code'],
            'name' => $contentType['layout']['name'],
        ]);

        foreach ($contentType['layout']['sections'] as $section) {
            $groupPosition = 0;

            foreach ($section['field_groups'] as $group) {
                $groupId = $this->uuidGenerator->generate();

                $this->connection->insert('#__content_type_layout_group', [
                    'id' => $groupId,
                    'code' => $group['code'],
                    'name' => $group['name'],
                    'section' => $section['code'],
                    'layout_type' => $contentType['layout']['code'],
                    '`order`' => $groupPosition++,
                ]);

                $fieldPosition = 0;
                foreach ($group['fields'] as $field) {
                    $this->connection->insert('#__content_type_layout_group_field', [
                        'group_id' => $groupId,
                        'code' => $field,
                        '`order`' => $fieldPosition++,
                    ]);
                }
            }
        }
    }

    public function update(array $contentType): void
    {
        // todo update data instead of remove and insert new one
        $this->delete($contentType);
        $this->insert($contentType);
    }

    public function delete(array $contentType): void
    {
        $this->connection->delete('#__content_type', ['code' => $contentType['code']]);
        $this->connection->delete('#__content_type_layout', ['code' => $contentType['layout']['code']]);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    private function collectDetailsForType(array $type): array
    {
        $type['fields'] = $this->collectFields($type['id']);
        $type['layout'] = $this->collectLayoutDefault($type['layout']);

        return $type;
    }

    private function collectFields(string $contentTypeId): array
    {
        $fields = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field WHERE content_type_id = :content_type_id',
            ['content_type_id' => $contentTypeId]
        );

        foreach ($fields as $key => $field) {
            $fields[$key]['has_nonscalar_value'] = (bool) $fields[$key]['has_nonscalar_value'];
            $fields[$key]['is_multilingual'] = (bool) $fields[$key]['is_multilingual'];
            $fields[$key]['configuration'] = $this->getFieldConfiguration($field['id']);
            $fields[$key]['constraints'] = $this->getFieldConstraints($field['id']);
        }

        return $this->sortFieldsHierarchically(null, $fields);
    }

    private function getFieldConfiguration(string $id): array
    {
        $configuration = [];
        $configurationSource = $this->connection->fetchAllAssociative(
            'SELECT code, value FROM #__content_type_field_configuration WHERE field_id = :field_id',
            ['field_id' => $id]
        );

        foreach ($configurationSource as $row) {
            $configuration[$row['code']] = $row['value'];
        }

        return $configuration;
    }

    private function getFieldConstraints(string $id): array
    {
        $constraints = [];
        $constraintsSource = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_field_constraint WHERE field_id = :field_id',
            ['field_id' => $id]
        );

        foreach ($constraintsSource as $constraint) {
            $modificators = [];
            $modificatorsSource = $this->connection->fetchAllAssociative(
                'SELECT modificator, value FROM #__content_type_field_constraint_modificator WHERE constraint_id = :constraint_id',
                ['constraint_id' => $constraint['id']]
            );

            foreach ($modificatorsSource as $modificator) {
                $modificators[$modificator['modificator']] = $modificator['value'];
            }

            $constraints[$constraint['code']] = [
                'modificators' => $modificators,
            ];
        }

        return $constraints;
    }

    private function collectLayoutDefault(string $code): array
    {
        $layout = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_layout WHERE code = :code',
            ['code' => $code]
        );

        if ($layout === []) {
            // @todo What to do when Layout is any case not exists in storage? Throw domain exception?
        }

        $layout = $layout[0];

        $sourceGroups = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__content_type_layout_group WHERE layout_type = :layout_type ORDER BY `order` ASC',
            ['layout_type' => $code]
        );

        foreach ($sourceGroups as $group) {
            $groupFields = $this->connection->fetchFirstColumn(
                'SELECT code FROM #__content_type_layout_group_field WHERE group_id = :group_id ORDER BY `order` ASC',
                ['group_id' => $group['id']]
            );

            $group['fields'] = $groupFields;
            $layout['sections'][$group['section']]['groups'][$group['code']] = $group;
        }

        return $layout;
    }

    private function sortFieldsHierarchically(?string $parent, array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field['parent'] === $parent) {
                $field['children'] = $this->sortFieldsHierarchically($field['code'], $fields);
                $result[] = $field;
            }
        }

        return $result;
    }
}
