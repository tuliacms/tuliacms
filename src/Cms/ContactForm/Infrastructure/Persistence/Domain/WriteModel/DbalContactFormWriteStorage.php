<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\ContactForm\Ports\Infrastructure\Persistence\Domain\WriteModel\ContactFormWriteStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalContactFormWriteStorage extends AbstractLocalizableStorage implements ContactFormWriteStorageInterface
{
    private ConnectionInterface $connection;

    private DbalFieldWriteStorage $fieldStorage;

    public function __construct(ConnectionInterface $connection, DbalFieldWriteStorage $fieldStorage)
    {
        $this->connection = $connection;
        $this->fieldStorage = $fieldStorage;
    }

    public function insert(array $data, string $defaultLocale): void
    {
        parent::insert($data, $defaultLocale);

        $this->storeFields($data['fields'], $defaultLocale);
    }

    public function update(array $data, string $defaultLocale): void
    {
        parent::update($data, $defaultLocale);

        $this->storeFields($data['fields'], $defaultLocale);
    }

    public function find(string $id, string $locale, string $defaultLocale): array
    {
        if ($defaultLocale !== $locale) {
            $translationColumn = 'IF(ISNULL(tl.name), 0, 1) AS translated';
        } else {
            $translationColumn = '1 AS translated';
        }

        $form = $this->connection->fetchAll("
            SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS locale,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.subject, tm.subject) AS subject,
                COALESCE(tl.message_template, tm.message_template) AS message_template,
                COALESCE(tl.fields_template, tm.fields_template) AS fields_template,
                {$translationColumn}
            FROM #__form AS tm
            LEFT JOIN #__form_lang AS tl
                ON tm.id = tl.form_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1", [
            'id' => $id,
            'locale' => $locale,
            'defaultLocale' => $defaultLocale,
        ]);

        if (empty($form)) {
            return [];
        }

        $form[0]['fields'] = $this->fieldStorage->find($id, $locale, $defaultLocale);

        return $form[0];
    }

    public function delete(array $form): void
    {
        $this->connection->delete('#__form', ['id' => $form['id']]);
        $this->connection->delete('#__form_lang', ['form_id' => $form['id']]);

        $this->fieldStorage->delete($form);
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
        $mainTable['id']               = $data['id'];
        $mainTable['website_id']       = $data['website_id'];
        $mainTable['receivers']        = $data['receivers'];
        $mainTable['sender_name']      = $data['sender_name'];
        $mainTable['sender_email']     = $data['sender_email'];
        $mainTable['reply_to']         = $data['reply_to'];
        $mainTable['name']             = $data['name'];
        $mainTable['subject']          = $data['subject'];
        $mainTable['message_template'] = $data['message_template'];
        $mainTable['fields_template']  = $data['fields_template'];
        $mainTable['fields_view']      = $data['fields_view'];

        $this->connection->insert('#__form', $mainTable);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['receivers']    = $data['receivers'];
        $mainTable['sender_name']  = $data['sender_name'];
        $mainTable['sender_email'] = $data['sender_email'];
        $mainTable['reply_to']     = $data['reply_to'];

        if ($foreignLocale === false) {
            $mainTable['name']             = $data['name'];
            $mainTable['subject']          = $data['subject'];
            $mainTable['message_template'] = $data['message_template'];
            $mainTable['fields_template']  = $data['fields_template'];
            $mainTable['fields_view']      = $data['fields_view'];
        }

        $this->connection->update('#__form', $mainTable, ['id' => $data['id']]);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['form_id']          = $data['id'];
        $langTable['locale']           = $data['locale'];
        $langTable['name']             = $data['name'];
        $langTable['subject']          = $data['subject'];
        $langTable['message_template'] = $data['message_template'];
        $langTable['fields_template']  = $data['fields_template'];
        $langTable['fields_view']      = $data['fields_view'];

        $this->connection->insert('#__form_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name']             = $data['name'];
        $langTable['subject']          = $data['subject'];
        $langTable['message_template'] = $data['message_template'];
        $langTable['fields_template']  = $data['fields_template'];
        $langTable['fields_view']      = $data['fields_view'];

        $this->connection->update('#__form_lang', $langTable, [
            'form_id' => $data['id'],
            'locale'  => $data['locale'],
        ]);
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT form_id FROM #__form_lang WHERE form_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['form_id']) && $result[0]['form_id'] === $data['id']->getId();
    }

    private function storeFields(array $fields, string $defaultLocale): void
    {
        foreach ($fields as $field) {
            if ($field['_change_type'] === 'add') {
                $this->fieldStorage->insert($field, $defaultLocale);
            } elseif ($field['_change_type'] === 'update') {
                $this->fieldStorage->update($field, $defaultLocale);
            } elseif ($field['_change_type'] === 'remove') {
                $this->fieldStorage->delete($field);
            }
        }
    }
}
