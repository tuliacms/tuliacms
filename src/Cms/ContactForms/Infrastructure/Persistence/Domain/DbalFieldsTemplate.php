<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFieldsTemplate
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getTemplatesForAllLocales(string $formId): array
    {
        $mainRow = $this->connection->fetchAll('
            SELECT fields_template
            FROM #__form
            WHERE id = :form_id
            LIMIT 1', [
            'form_id' => $formId,
        ]);

        $langRows = $this->connection->fetchAll('
            SELECT fields_template, locale
            FROM #__form_lang
            WHERE form_id = :form_id', [
            'form_id' => $formId,
        ]);

        return array_merge($mainRow, $langRows);
    }
}
