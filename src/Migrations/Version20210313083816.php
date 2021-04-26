<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210313083816 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__activity` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `website_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(127) COLLATE utf8_unicode_ci NOT NULL,
  `translation_domain` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'messages',
  `context` json NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__activity`
  ADD PRIMARY KEY (`id`);
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__activity``');
    }
}
