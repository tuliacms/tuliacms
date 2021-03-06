<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210311085344 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__term` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` smallint(6) NOT NULL DEFAULT '0',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `global_order` bigint(20) NOT NULL DEFAULT '0',
  `count` int(10) UNSIGNED DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__term_lang` (
  `term_id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__term_metadata` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__term_metadata_lang` (
  `metadata_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__term_path` (
  `term_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`website_id`,`type`),
  ADD KEY `fk_term_parent_id` (`parent_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_lang`
  ADD PRIMARY KEY (`term_id`,`locale`) USING BTREE,
  ADD KEY `search_slug` (`slug`(191),`locale`,`visibility`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_88CBC8F8E2C35FC` (`owner_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_metadata_lang`
  ADD KEY `IDX_C93142EEDC9EE959` (`metadata_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_path`
  ADD PRIMARY KEY (`path`,`locale`) USING BTREE,
  ADD KEY `fk_term_path_term_id` (`term_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term`
  ADD CONSTRAINT `fk_term_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_term_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_lang`
  ADD CONSTRAINT `fk_term_lang_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_metadata`
  ADD CONSTRAINT `FK_88CBC8F8E2C35FC` FOREIGN KEY (`owner_id`) REFERENCES `#__term` (`id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_metadata_lang`
  ADD CONSTRAINT `FK_C93142EEDC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `#__term_metadata` (`id`) ON DELETE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__term_path`
  ADD CONSTRAINT `fk_term_path_term_id` FOREIGN KEY (`term_id`) REFERENCES `#__term` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__term`');
        $this->addSql('DROP TABLE `#__term_lang`');
        $this->addSql('DROP TABLE `#__term_metadata`');
        $this->addSql('DROP TABLE `#__term_metadata_lang`');
        $this->addSql('DROP TABLE `#__term_path`');
    }
}
