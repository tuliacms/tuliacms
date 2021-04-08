<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313084203 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__filemanager_directory` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__filemanager_file` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `directory` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '00000000-0000-0000-0000-000000000000',
  `filename` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'file',
  `mimetype` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `path` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__filemanager_image_thumbnail` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `file_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `size` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(127) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__filemanager_directory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent` (`parent_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__filemanager_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `directory` (`directory`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__filemanager_image_thumbnail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`,`size`);
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__filemanager_directory`');
        $this->addSql('DROP TABLE `#__filemanager_file`');
        $this->addSql('DROP TABLE `#__filemanager_image_thumbnail`');
    }
}
