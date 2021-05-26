<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain;

use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFormStorage extends AbstractLocalizableStorage
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['receivers']    = json_encode($data['receivers']);
        $mainTable['sender_name']  = $data['senderName'];
        $mainTable['sender_email'] = $data['senderEmail'];
        $mainTable['reply_to']     = $data['replyTo'];

        if ($foreignLocale === false) {
            $mainTable['name']             = $data['name'];
            $mainTable['subject']          = $data['subject'];
            $mainTable['message_template'] = $data['messageTemplate'];
            $mainTable['fields_template']  = $data['fieldsTemplate'];
        }

        $this->connection->update('#__form', $mainTable, ['id' => $data['id']]);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id']               = $data['id'];
        $mainTable['website_id']       = $data['websiteId'];
        $mainTable['receivers']        = json_encode($data['receivers']);
        $mainTable['sender_name']      = $data['senderName'];
        $mainTable['sender_email']     = $data['senderEmail'];
        $mainTable['reply_to']         = $data['replyTo'];
        $mainTable['name']             = $data['name'];
        $mainTable['subject']          = $data['subject'];
        $mainTable['message_template'] = $data['messageTemplate'];
        $mainTable['fields_template']  = $data['fieldsTemplate'];

        $this->connection->insert('#__form', $mainTable);
    }

    protected function insertLangRow(array $data): void
    {
        $langTable = [];
        $langTable['form_id']          = $data['id'];
        $langTable['locale']           = $data['locale'];
        $langTable['name']             = $data['name'];
        $langTable['subject']          = $data['subject'];
        $langTable['message_template'] = $data['messageTemplate'];
        $langTable['fields_template']  = $data['fieldsTemplate'];

        $this->connection->insert('#__form_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        $langTable = [];
        $langTable['name']             = $data['name'];
        $langTable['subject']          = $data['subject'];
        $langTable['message_template'] = $data['messageTemplate'];
        $langTable['fields_template']  = $data['fieldsTemplate'];

        $this->connection->update('#__form_lang', $langTable, [
            'form_id' => $data['id'],
            'locale'  => $data['locale'],
        ]);
    }

    protected function rootExists(string $id): bool
    {
        $result = $this->connection->fetchAllAssociative('SELECT id FROM #__form WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }

    protected function langExists(array $data): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT form_id FROM #__form_lang WHERE form_id = :id AND locale = :locale LIMIT 1',
            ['id' => $data['id'], 'locale' => $data['locale']]
        );

        return isset($result[0]['form_id']) && $result[0]['form_id'] === $data['id'];
    }

    public function delete(string $formId): void
    {
        $this->connection->delete('#__form', ['id' => $formId]);
    }
}
