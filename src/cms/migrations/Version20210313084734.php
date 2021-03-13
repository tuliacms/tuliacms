<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313084734 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__menu` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__menu_item` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menu_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(10) UNSIGNED DEFAULT '0',
  `level` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__menu_item_lang` (
  `menu_item_id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__menu_item_metadata` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `menu_item_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__menu_item_metadata_lang` (
  `metadata_id` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `website_id` (`website_id`) USING BTREE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B3378EA2727ACA70` (`parent_id`),
  ADD KEY `IDX_B3378EA2CCD7E912` (`menu_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_lang`
  ADD KEY `menu_item_id` (`menu_item_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1CADB6F19AB44FE0` (`menu_item_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_metadata_lang`
  ADD KEY `IDX_16B60FBCDC9EE959` (`metadata_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu`
  ADD CONSTRAINT `fk_menu_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item`
  ADD CONSTRAINT `menu_item_menu_id` FOREIGN KEY (`menu_id`) REFERENCES `#__menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_item_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `#__menu_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_lang`
  ADD CONSTRAINT `menu_item_lang_menu_item_id` FOREIGN KEY (`menu_item_id`) REFERENCES `#__menu_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_metadata`
  ADD CONSTRAINT `FK_1CADB6F19AB44FE0` FOREIGN KEY (`menu_item_id`) REFERENCES `#__menu_item` (`id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__menu_item_metadata_lang`
  ADD CONSTRAINT `FK_16B60FBCDC9EE959` FOREIGN KEY (`metadata_id`) REFERENCES `#__menu_item_metadata` (`id`) ON DELETE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__menu`');
        $this->addSql('DROP TABLE `#__menu_item`');
        $this->addSql('DROP TABLE `#__menu_item_lang`');
        $this->addSql('DROP TABLE `#__menu_item_metadata`');
        $this->addSql('DROP TABLE `#__menu_item_metadata_lang`');
    }
}
