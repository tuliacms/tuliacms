<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313084956 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__node` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `level` tinyint(4) DEFAULT '0',
  `author_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'page',
  `status` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sketch',
  `published_at` datetime DEFAULT NULL,
  `published_to` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `introduction` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_compiled` longtext COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__node_lang` (
  `node_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `introduction` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  `content_compiled` longtext COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__node_metadata` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__node_metadata_lang` (
  `metadata_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__node_term_relationship` (
  `node_id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `term_id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('MAIN','ADDITIONAL','AUTO') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__content_type` (
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `supports` text COLLATE utf8_unicode_ci,
  `is_routable` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_domain` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `taxonomies` text COLLATE utf8_unicode_ci,
  `parameters` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__node_has_flag` (
  `node_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `flag` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
        );
        $this->addSql(<<<EOL
ALTER TABLE `#__node`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `website_id` (`website_id`),
  ADD KEY `author_id` (`author_id`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_lang`
  ADD KEY `node_id` (`node_id`) USING BTREE,
  ADD KEY `search_slug` (`slug`(191),`locale`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_lang` ADD FULLTEXT KEY `title` (`title`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_metadata_lang`
  ADD KEY `metadata_id` (`metadata_id`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_term_relationship`
  ADD PRIMARY KEY (`node_id`,`term_id`),
  ADD KEY `node_id` (`node_id`) USING BTREE,
  ADD KEY `term_id` (`term_id`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__content_type`
  ADD PRIMARY KEY (`type`),
  ADD KEY `active` (`active`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_has_flag`
  ADD UNIQUE KEY `UNIQUE` (`node_id`,`flag`),
  ADD KEY `node_id` (`node_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node`
  ADD CONSTRAINT `fk_node_author_id` FOREIGN KEY (`author_id`) REFERENCES `#__user` (`id`) ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_node_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `#__node` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_node_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_lang`
  ADD CONSTRAINT `fk_node_lang_node_id` FOREIGN KEY (`node_id`) REFERENCES `#__node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_metadata`
  ADD CONSTRAINT `fk_node_metadata_node_id` FOREIGN KEY (`owner_id`) REFERENCES `#__node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_metadata_lang`
  ADD CONSTRAINT `fk_node_metadata_lang_metadata_id` FOREIGN KEY (`metadata_id`) REFERENCES `#__node_metadata` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_term_relationship`
  ADD CONSTRAINT `fk_node_term_relationship_node_id` FOREIGN KEY (`node_id`) REFERENCES `#__node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_node_term_relationship_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__node_has_flag`
  ADD CONSTRAINT `fk_node_has_flag_node_id` FOREIGN KEY (`node_id`) REFERENCES `#__node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__content_type`');
        $this->addSql('DROP TABLE `#__node_term_relationship`');
        $this->addSql('DROP TABLE `#__node_metadata_lang`');
        $this->addSql('DROP TABLE `#__node_metadata`');
        $this->addSql('DROP TABLE `#__node_lang`');
        $this->addSql('DROP TABLE `#__node`');
    }
}
