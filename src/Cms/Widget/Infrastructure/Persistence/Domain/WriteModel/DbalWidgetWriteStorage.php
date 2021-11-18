<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Widget\Ports\Infrastructure\Persistence\Domain\WriteModel\WidgetWriteStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalWidgetWriteStorage extends AbstractLocalizableStorage implements WidgetWriteStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $id, string $locale, string $defaultLocale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.title), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        $widget = $this->connection->fetchAllAssociative("
            SELECT
                tm.*,
                tm.widget_type,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized) AS payload_localized,
                COALESCE(tl.locale, :locale) AS locale,
                {$translationColumn}
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tm.id = tl.widget_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1", [
            'id'     => $id,
            'locale' => $locale
        ]);

        return $widget[0] ?? [];
    }

    public function delete(array $widget): void
    {
        $this->connection->delete('#__widget', ['id' => $widget['id']]);
        $this->connection->delete('#__widget_lang', ['widget_id' => $widget['id']]);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollback();
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['space'] = $data['space'];
        $mainTable['widget_type'] = $data['widget_type'];
        $mainTable['name'] = $data['name'];
        $mainTable['title'] = $data['title'];
        $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
        $mainTable['html_class'] = $data['html_class'];
        $mainTable['html_id'] = $data['html_id'];
        $mainTable['styles'] = $data['styles'];
        $mainTable['payload'] = $data['payload'];
        $mainTable['payload_localized'] = $data['payload_localized'];

        $this->connection->insert('#__widget', $mainTable);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['space'] = $data['space'];
        $mainTable['widget_type'] = $data['widget_type'];
        $mainTable['name'] = $data['name'];
        $mainTable['html_class'] = $data['html_class'];
        $mainTable['html_id'] = $data['html_id'];
        $mainTable['styles'] = $data['styles'];
        $mainTable['payload'] = $data['payload'];

        if ($foreignLocale === false) {
            $mainTable['title'] = $data['title'];
            $mainTable['visibility'] = $data['visibility'] ? '1' : '0';
            $mainTable['payload_localized'] = $data['payload_localized'];
        }

        $this->connection->update('#__widget', $mainTable, ['id' => $data['id']]);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['widget_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['title'] = $data['title'];
        $langTable['payload_localized'] = $data['payload_localized'];

        $this->connection->insert('#__widget_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['title'] = $data['title'];
        $langTable['payload_localized'] = $data['payload_localized'];

        $this->connection->update('#__widget_lang', $langTable, [
            'widget_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT widget_id FROM #__widget_lang WHERE widget_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['widget_id']) && $result[0]['widget_id'] === $data['id'];
    }
}
