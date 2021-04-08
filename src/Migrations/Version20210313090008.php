<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313090008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__widget` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `space` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `widget_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `html_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `html_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `styles` json DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `payload_localized` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__widget_lang` (
  `widget_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0',
  `payload_localized` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__widget`
  ADD PRIMARY KEY (`id`),
  ADD KEY `space` (`space`),
  ADD KEY `website_id` (`website_id`),
  ADD KEY `fk_widget_website_id` (`widget_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__widget_lang`
  ADD UNIQUE KEY `widget_id` (`widget_id`,`locale`),
  ADD KEY `locale` (`locale`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__widget`
  ADD CONSTRAINT `fk_widget_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__widget_lang`
  ADD CONSTRAINT `#__widget_lang_ibfk_1` FOREIGN KEY (`widget_id`) REFERENCES `#__widget` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__widget_lang`');
        $this->addSql('DROP TABLE `#__widget`');
    }
}
