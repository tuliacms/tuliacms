<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313084355 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__form` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `receivers` json NOT NULL,
  `sender_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sender_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reply_to` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message_template` text COLLATE utf8_unicode_ci,
  `fields_view` text COLLATE utf8_unicode_ci,
  `fields_template` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__form_field` (
  `form_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `options` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__form_field_lang` (
  `form_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `options` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
CREATE TABLE `#__form_lang` (
  `form_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en_US',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message_template` text COLLATE utf8_unicode_ci NOT NULL,
  `fields_view` text COLLATE utf8_unicode_ci,
  `fields_template` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_form_website_id` (`website_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_field`
  ADD PRIMARY KEY (`form_id`,`name`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_field_lang`
  ADD PRIMARY KEY (`form_id`,`name`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_lang`
  ADD KEY `form_lang_form_id` (`form_id`);
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form`
  ADD CONSTRAINT `fk_form_website_id` FOREIGN KEY (`website_id`) REFERENCES `#__website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_field`
  ADD CONSTRAINT `fk_form_field_form_id` FOREIGN KEY (`form_id`) REFERENCES `#__form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_field_lang`
  ADD CONSTRAINT `fk_form_field_lang_form_id` FOREIGN KEY (`form_id`) REFERENCES `#__form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__form_lang`
  ADD CONSTRAINT `form_lang_form_id` FOREIGN KEY (`form_id`) REFERENCES `#__form` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__form`');
        $this->addSql('DROP TABLE `#__form_field`');
        $this->addSql('DROP TABLE `#__form_field_lang`');
        $this->addSql('DROP TABLE `#__form_lang`');
    }
}
