<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313085231 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__option` (
  `name` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `multilingual` tinyint(1) NOT NULL DEFAULT '0',
  `autoload` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__option_lang` (
  `name` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__option`
  ADD PRIMARY KEY (`name`,`website_id`),
  ADD KEY `website_id` (`website_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__option_lang`
  ADD PRIMARY KEY (`name`,`locale`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__option`
  ADD CONSTRAINT `fk_option_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__option`');
        $this->addSql('DROP TABLE `#__option_lang`');
    }
}
