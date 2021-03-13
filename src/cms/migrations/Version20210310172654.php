<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20210310172654 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<EOL
CREATE TABLE `#__parameter` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `type` enum('text','array','datetime') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOL
);
        $this->addSql(<<<EOL
ALTER TABLE `#__parameter`
  ADD PRIMARY KEY (`name`);
EOL
);

        $this->addSql(<<<EOL
INSERT INTO `#__parameter` (`name`, `value`, `type`) VALUES
('modules.enabled.list', '[]', 'array');
EOL
);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `#__parameter`');
    }
}
