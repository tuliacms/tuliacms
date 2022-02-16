<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20220110200000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOF
CREATE TABLE `#__content_type` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_routable` tinyint(1) NOT NULL DEFAULT '0',
  `is_hierarchical` tinyint(1) NOT NULL DEFAULT '0',
  `routing_strategy` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `layout` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `content_type_id` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_multilingual` tinyint(1) NOT NULL DEFAULT '0',
  `taxonomy` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_configuration` (
  `field_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_constraint` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `field_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(36) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_field_constraint_modificator` (
  `constraint_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `modificator` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_layout` (
  `code` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_layout_group` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `section` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `layout_type` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `interior` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `order` smallint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
CREATE TABLE `#__content_type_layout_group_field` (
  `group_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(127) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type`
  ADD PRIMARY KEY (`code`),
  ADD KEY `fk_node_type_layout_type` (`layout`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`),
  ADD KEY `fk_content_type_field_content_type_id` (`content_type_id`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_configuration`
  ADD KEY `node_type_field_configuration_field_id` (`field_id`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_constraint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_node_type_field_constraint_field_id` (`field_id`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_constraint_modificator`
  ADD KEY `fk_node_type_field_constraint_modificator_constraint_id` (`constraint_id`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_layout`
  ADD PRIMARY KEY (`code`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_layout_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `node_type_layout_group_layout_type` (`layout_type`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_layout_group_field`
  ADD KEY `fk_node_type_layout_group_field_group_id` (`group_id`);
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field`
  ADD CONSTRAINT `fk_content_type_field_content_type_id` FOREIGN KEY (`content_type_id`) REFERENCES `#__content_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_configuration`
  ADD CONSTRAINT `node_type_field_configuration_field_id` FOREIGN KEY (`field_id`) REFERENCES `#__content_type_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_constraint`
  ADD CONSTRAINT `fk_node_type_field_constraint_field_id` FOREIGN KEY (`field_id`) REFERENCES `#__content_type_field` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_field_constraint_modificator`
  ADD CONSTRAINT `fk_node_type_field_constraint_modificator_constraint_id` FOREIGN KEY (`constraint_id`) REFERENCES `#__content_type_field_constraint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_layout_group`
  ADD CONSTRAINT `node_type_layout_group_layout_type` FOREIGN KEY (`layout_type`) REFERENCES `#__content_type_layout` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);

        $this->addSql(<<<EOF
ALTER TABLE `#__content_type_layout_group_field`
  ADD CONSTRAINT `fk_node_type_layout_group_field_group_id` FOREIGN KEY (`group_id`) REFERENCES `#__content_type_layout_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOF);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE #__content_type');
        $this->addSql('DROP TABLE #__content_type_field');
        $this->addSql('DROP TABLE #__content_type_field_configuration');
        $this->addSql('DROP TABLE #__content_type_field_constraint');
        $this->addSql('DROP TABLE #__content_type_field_constraint_modificator');
        $this->addSql('DROP TABLE #__content_type_layout');
        $this->addSql('DROP TABLE #__content_type_layout_group');
        $this->addSql('DROP TABLE #__content_type_layout_group_field');
    }
}
