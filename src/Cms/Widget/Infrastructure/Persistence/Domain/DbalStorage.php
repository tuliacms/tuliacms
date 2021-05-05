<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Persistence\Domain;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalStorage extends AbstractLocalizableStorage
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['id']         = $data['id'];
        $mainTable['space']      = $data['space'];
        $mainTable['widget_id']  = $data['widgetId'];
        $mainTable['website_id'] = $data['websiteId'];
        $mainTable['name']       = $data['name'];
        $mainTable['html_class'] = $data['htmlClass'];
        $mainTable['html_id']    = $data['htmlId'];
        $mainTable['styles']     = json_encode(\is_array($data['styles']) ? $data['styles'] : []);
        $mainTable['payload']    = json_encode(\is_array($data['payload']) ? $data['payload'] : []);

        if ($foreignLocale === false) {
            $mainTable['title']      = $data['title'];
            $mainTable['visibility'] = $data['visibility'];
            $mainTable['payload_localized'] = json_encode(\is_array($data['payloadLocalized']) ? $data['payloadLocalized'] : []);
        }

        $this->connection->update('#__widget', $mainTable, ['id' => $data['id']]);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id']         = $data['id'];
        $mainTable['space']      = $data['space'];
        $mainTable['widget_id']  = $data['widgetId'];
        $mainTable['website_id'] = $data['websiteId'];
        $mainTable['name']       = $data['name'];
        $mainTable['title']      = $data['title'];
        $mainTable['visibility'] = $data['visibility'];
        $mainTable['html_class'] = $data['htmlClass'];
        $mainTable['html_id']    = $data['htmlId'];
        $mainTable['styles']     = json_encode(\is_array($data['styles']) ? $data['styles'] : []);
        $mainTable['payload']    = json_encode(\is_array($data['payload']) ? $data['payload'] : []);
        $mainTable['payload_localized'] = json_encode(\is_array($data['payloadLocalized']) ? $data['payloadLocalized'] : []);

        $this->connection->insert('#__widget', $mainTable);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['widget_id']  = $data['id'];
        $langTable['locale']     = $data['locale'];
        $langTable['title']      = $data['title'];
        $langTable['visibility'] = $data['visibility'];
        $langTable['payload_localized'] = json_encode(\is_array($data['payloadLocalized']) ? $data['payloadLocalized'] : []);

        $this->connection->insert('#__widget_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['title']      = $data['title'];
        $langTable['visibility'] = $data['visibility'];
        $langTable['payload_localized'] = json_encode(\is_array($data['payloadLocalized']) ? $data['payloadLocalized'] : []);

        $this->connection->update('#__widget_lang', $langTable, [
            'widget_id' => $data['id'],
            'locale'    => $data['locale'],
        ]);
    }

    protected function rootExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__widget WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    protected function langExists(string $id, string $locale): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT widget_id FROM #__widget_lang WHERE widget_id = :id AND locale = :locale LIMIT 1',
            ['id' => $id, 'locale' => $locale]
        );

        return isset($result[0]['widget_id']) && $result[0]['widget_id'] === $id;
    }

    public function delete(string $formId): void
    {
        $this->connection->delete('#__widget', ['id' => $formId]);
    }
}
