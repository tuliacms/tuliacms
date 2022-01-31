<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\Domain;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

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

    public function find(string $id): array
    {
        // TODO: Implement find() method.
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
            'internal' => $contentType['is_internal'] ? '1' : '0',
        ]);

        foreach ($contentType['fields'] as $field) {
            $fieldId = $this->uuidGenerator->generate();

            $this->connection->insert('#__content_type_field', [
                'id' => $fieldId,
                'code' => $field['code'],
                'content_type_id' => $contentType['id'],
                'type' => $field['type'],
                'name' => $field['name'],
                'is_multilingual' => $field['is_multilingual'] ? '1' : '0',
                'is_multiple' => $field['is_multiple'] ? '1' : '0',
                //'taxonomy' => $field['taxonomy'],
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
                    'interior' => $group['interior'],
                    'active' => $group['active'] ? '1' : '0',
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
}
