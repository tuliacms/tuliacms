<?php

declare(strict_types=1);

namespace Tulia\Cms\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
final class Version20220117185000 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $contentTypeId = Uuid::uuid4()->toString();

        $this->addSql('INSERT INTO #__content_type (`code`, `type`, `name`, `icon`, `is_routable`, `is_hierarchical`, `layout`, `internal`, `routing_strategy`) VALUES (:code, :type, :name, :icon, :is_routable, :is_hierarchical, :layout, :internal, :routing_strategy)', [
            'id' => $contentTypeId,
            'code' => 'category',
            'type' => 'taxonomy',
            'name' => 'Category',
            'icon' => 'fas fa-folder-open',
            'is_routable' => '1',
            'is_hierarchical' => '1',
            'internal' => '1',
            'layout' => 'category_layout',
            'routing_strategy' => 'full_path',
        ]);

        $this->addSql('INSERT INTO #__content_type_layout (`code`, `name`) VALUES (:code, :name)', [
            'code' => 'category_layout',
            'name' => 'Category layout',
        ]);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM #__content_type WHERE `code` = 'category'");
        $this->addSql("DELETE FROM #__content_type_layout WHERE `code` = 'category_layout'");
    }
}
