<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210311085722 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__user` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `enabled` int(11) NOT NULL DEFAULT '1',
  `account_expired` int(11) NOT NULL DEFAULT '0',
  `credentials_expired` int(11) NOT NULL DEFAULT '0',
  `account_locked` int(11) NOT NULL DEFAULT '0',
  `roles` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__user_metadata` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__user_metadata_lang` (
  `metadata_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__user`
  ADD UNIQUE KEY `id` (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__user_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E710524CA76ED395` (`user_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__user_metadata_lang`
  ADD KEY `IDX_9D324043DC9EE959` (`metadata_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__user_metadata`
  ADD CONSTRAINT `FK_E710524CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `#__user` (`id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__user_metadata_lang`
  ADD CONSTRAINT `FK_9D324043DC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `#__user_metadata` (`id`) ON DELETE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__user`');
        $this->addSql('DROP TABLE `#__user_metadata`');
        $this->addSql('DROP TABLE `#__user_metadata_lang`');
    }
}
