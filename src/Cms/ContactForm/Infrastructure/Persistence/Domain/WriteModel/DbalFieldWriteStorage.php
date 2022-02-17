<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFieldWriteStorage extends AbstractLocalizableStorage
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $id, string $locale, string $defaultLocale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.options), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        return $this->connection->fetchAllAssociative("
            SELECT
                tm.name,
                tm.type,
                tm.type_alias,
                COALESCE(tl.locale, :locale) AS locale,
                COALESCE(tl.options, tm.options) AS options,
                {$translationColumn}
            FROM #__form_field AS tm
            LEFT JOIN #__form_field_lang AS tl
                ON tl.form_id = :form_id AND tl.name = tm.name AND tl.locale = :locale
            WHERE tm.form_id = :form_id", [
            'form_id' => $id,
            'locale' => $locale,
            'defaultLocale' => $defaultLocale,
        ]);
    }

    public function delete(array $field): void
    {
        $this->connection->delete('#__form_field', ['name' => $field['name'], 'form_id' => $field['form_id']]);
        $this->connection->delete('#__form_field_lang', ['name' => $field['name'], 'form_id' => $field['form_id']]);
    }

    protected function insertMainRow(array $data): void
    {
        $this->connection->insert('#__form_field', [
            'form_id' => $data['form_id'],
            'name' => $data['name'],
            'type' => $data['type'],
            'type_alias' => $data['type_alias'],
            'options' => $data['options'],
        ]);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['name'] = $data['name'];
        $mainTable['type'] = $data['type'];
        $mainTable['type_alias'] = $data['type_alias'];

        if ($foreignLocale === false) {
            $mainTable['options'] = $data['options'];
        }

        $this->connection->update('#__form_field', $mainTable, [
            'form_id' => $data['form_id'],
            'name' => $data['name'],
        ]);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['form_id'] = $data['form_id'];
        $langTable['locale'] = $data['locale'];
        $langTable['name'] = $data['name'];
        $langTable['options'] = $data['options'];

        $this->connection->insert('#__form_field_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['options'] = $data['options'];

        $this->connection->update('#__form_field_lang', $langTable, [
            'form_id' => $data['form_id'],
            'name' => $data['name'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT name FROM #__form_field_lang WHERE form_id = :form_id AND name = :name AND locale = :locale LIMIT 1',
            ['form_id' => $data['form_id']->getId(), 'name' => $data['name'], 'locale' => $data['locale']]
        );

        return isset($result[0]['name']) && $result[0]['name'] === $data['name'];
    }
}
