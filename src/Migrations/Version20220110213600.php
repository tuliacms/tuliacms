<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20220110213600 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO #__node_type (`code`, `name`, `icon`, `is_routable`, `is_hierarchical`, `layout`) VALUES (:code, :name, :icon, :is_routable, :is_hierarchical, :layout)', [
            'code' => 'page',
            'name' => 'Page',
            'icon' => 'fas fa-file-powerpoint',
            'is_routable' => '1',
            'is_hierarchical' => '1',
            'layout' => 'page_layout',
        ]);

        $this->addSql('INSERT INTO #__node_type_layout (`code`, `name`) VALUES (:code, :name)', [
            'code' => 'page_layout',
            'name' => 'Page layout',
        ]);

        $this->addGroup([
            'id' => Uuid::uuid4()->toString(),
            'code' => 'introduction',
            'name' => 'Introduction',
            'section' => 'main',
            'layout_type' => 'page_layout',
            'active' => '0',
            'order' => '0',
            'fields' => ['introduction'],
        ]);
        $this->addGroup([
            'id' => Uuid::uuid4()->toString(),
            'code' => 'content',
            'name' => 'Content',
            'section' => 'main',
            'layout_type' => 'page_layout',
            'active' => '1',
            'order' => '1',
            'fields' => ['content'],
        ]);
        $this->addGroup([
            'id' => Uuid::uuid4()->toString(),
            'code' => 'category',
            'name' => 'Category',
            'section' => 'sidebar',
            'layout_type' => 'page_layout',
            'active' => '0',
            'order' => '0',
            'fields' => ['category', 'tags'],
        ]);

        $this->addGroup([
            'id' => Uuid::uuid4()->toString(),
            'code' => 'thumbnail',
            'name' => 'Thumbnail',
            'section' => 'sidebar',
            'layout_type' => 'page_layout',
            'active' => '0',
            'order' => '1',
            'fields' => ['thumbnail'],
        ]);

        $this->addField([
            'code' => 'introduction',
            'node_type' => 'page',
            'type' => 'textarea',
            'name' => 'Introduction',
            'is_multilingual' => '1',
            'constraints' => [
                [
                    'code' => 'length',
                    'modificators' => [
                        [
                            'modificator' => 'max',
                            'value' => 255,
                        ],
                    ],
                ],
            ],
        ]);
        $this->addField([
            'code' => 'content',
            'node_type' => 'page',
            'type' => 'wysiwyg',
            'name' => 'Content',
            'is_multilingual' => '1',
        ]);
        $this->addField([
            'code' => 'category',
            'node_type' => 'page',
            'type' => 'taxonomy',
            'name' => 'Introduction',
            'taxonomy' => 'category',
            'is_multilingual' => '0',
        ]);
        $this->addField([
            'code' => 'tags',
            'node_type' => 'page',
            'type' => 'taxonomy',
            'name' => 'Introduction',
            'taxonomy' => 'tags',
            'is_multilingual' => '0',
        ]);
        $this->addField([
            'code' => 'thumbnail',
            'node_type' => 'page',
            'type' => 'filepicker',
            'name' => 'Thumbnail',
            'is_multilingual' => '0',
        ]);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM #__node_type WHERE `code` = 'page'");
        $this->addSql("DELETE FROM #__node_type_layout WHERE `code` = 'page_layout'");
    }

    private function addField(array $field): void
    {
        $fieldId = Uuid::uuid4()->toString();

        $this->addSql('INSERT INTO #__node_type_field (`id`, `code`, `node_type`, `type`, `name`, `taxonomy`, `is_multilingual`, `is_multiple`) VALUES (:id, :code, :node_type, :type, :name, :taxonomy, :is_multilingual, :is_multiple)', [
            'id' => $fieldId,
            'code' => $field['code'],
            'node_type' => $field['node_type'],
            'type' => $field['type'],
            'name' => $field['name'],
            'taxonomy' => $field['taxonomy'] ?? null,
            'is_multilingual' => $field['is_multilingual'] ?? '0',
            'is_multiple' => $field['is_multiple'] ?? '0',
        ]);

        foreach ($field['constraints'] ?? [] as $constraint) {
            $constraintId = Uuid::uuid4()->toString();

            $this->addSql('INSERT INTO #__node_type_field_constraint (`id`, `field_id`, `code`) VALUES (:id, :field_id, :code)', [
                'id' => $constraintId,
                'code' => $constraint['code'],
                'field_id' => $fieldId,
            ]);

            foreach ($constraint['modificators'] ?? [] as $modificator) {
                $this->addSql('INSERT INTO #__node_type_field_constraint_modificator (`constraint_id`, `modificator`, `value`) VALUES (:constraint_id, :modificator, :value)', [
                    'constraint_id' => $constraintId,
                    'modificator' => $modificator['modificator'],
                    'value' => $modificator['value'],
                ]);
            }
        }
    }

    private function addGroup(array $group): void
    {
        $fields = $group['fields'];
        unset($group['fields']);

        $this->addSql('INSERT INTO #__node_type_layout_group (`id`, `code`, `name`, `section`, `layout_type`, `active`, `order`) VALUES (:id, :code, :name, :section, :layout_type, :active, :order)', $group);

        foreach ($fields as $field) {
            $this->addSql('INSERT INTO #__node_type_layout_group_field (`group_id`, `code`) VALUES (:group_id, :code)', [
                'group_id' => $group['id'],
                'code' => $field,
            ]);
        }
    }
}
