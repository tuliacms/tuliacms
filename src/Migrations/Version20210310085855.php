<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210310085855 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__website` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `backend_prefix` varchar(36) COLLATE utf8_unicode_ci NOT NULL DEFAULT '/administrator'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__website_locale` (
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `domain_development` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path_prefix` varchar(127) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ssl_mode` enum('FORCE_SSL','FORCE_NON_SSL','ALLOWED_BOTH') COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale_prefix` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__website`
  ADD PRIMARY KEY (`id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__website_locale`
  ADD UNIQUE KEY `UNIQUE` (`website_id`,`code`,`domain`) USING BTREE,
  ADD KEY `website_id` (`website_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__website_locale`
  ADD CONSTRAINT `fk_website_has_locale_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__website`');
        $this->addSql('DROP TABLE `#__website_locale`');
    }
}
