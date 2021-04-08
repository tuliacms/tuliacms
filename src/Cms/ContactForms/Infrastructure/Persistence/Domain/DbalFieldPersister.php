<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain;

use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFieldPersister
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

    public function save(string $formId, array $fields, string $fieldsView, string $locale, string $defaultLocale): void
    {
        $foreignLocale = $defaultLocale !== $locale;

        $this->updateFieldsView($formId, $locale, $fieldsView, $foreignLocale);

        $currentFields = $this->fetchCurrentFields($formId, $foreignLocale ? $locale : null);
        $currentFieldsMap = array_column($currentFields, 'name', 'name');

        $new = array_keys($fields);
        $old = array_keys($currentFieldsMap);

        $toUpdate = array_intersect($old, $new);
        $toAdd    = array_diff($new, $toUpdate);
        $toRemove = array_diff($old, $toUpdate);

        foreach ($toAdd as $name) {
            $this->insertField($fields[$name], $foreignLocale);
        }

        foreach ($toUpdate as $name) {
            $this->updateField($fields[$name], $foreignLocale);
        }

        foreach ($toRemove as $name) {
            $this->removeField($formId, $name, $foreignLocale);
        }
    }

    public function delete(string $formId): void
    {
        $this->connection->delete('#__form_field', ['form_id' => $formId]);
        $this->connection->delete('#__form_field_lang', ['form_id' => $formId]);
    }

    private function insertField(array $field, bool $foreignLocale): void
    {
        if ($foreignLocale) {
            $this->insertLangRow($field);
        } else {
            $this->insertMainRow($field);
        }
    }

    private function updateField(array $field, bool $foreignLocale): void
    {
        if ($foreignLocale) {
            $this->updateLangRow($field);
        } else {
            $this->updateMainRow($field);
        }
    }

    private function insertMainRow(array $field): void
    {
        $mainTable = [];
        $mainTable['form_id'] = $field['form_id'];
        $mainTable['name']    = $field['name'];
        $mainTable['type']    = $field['type'];
        $mainTable['options'] = $field['options'];

        $this->connection->insert('#__form_field', $mainTable);
    }

    private function insertLangRow(array $field): void
    {
        $langTable = [];
        $langTable['form_id']  = $field['form_id'];
        $langTable['name']     = $field['name'];
        $langTable['locale']   = $field['locale'];
        $langTable['type']     = $field['type'];
        $langTable['options']  = $field['options'];

        $this->connection->insert('#__form_field_lang', $langTable);
    }

    private function updateMainRow(array $field): void
    {
        $this->connection->update('#__form_field', [
            'name'    => $field['name'],
            'type'    => $field['type'],
            'options' => $field['options'],
        ], [
            'form_id' => $field['form_id'],
            'name'    => $field['name'],
        ]);
    }

    private function updateLangRow(array $field): void
    {
        $this->connection->update('#__form_field_lang', [
            'options' => $field['options'],
            'type'    => $field['type'],
        ], [
            'form_id' => $field['form_id'],
            'name'    => $field['name'],
            'locale'  => $field['locale'],
        ]);
    }

    private function removeField(string $formId, string $name, bool $foreignLocale): void
    {
        if ($foreignLocale) {
            $this->connection->delete('#__form_field_lang', ['form_id' => $formId, 'name' => $name]);
        } else {
            $this->connection->delete('#__form_field', ['form_id' => $formId, 'name' => $name]);
        }
    }

    public function updateFieldsView(string $formId, string $locale, string $fieldsView, bool $foreignLocale): void
    {
        $data = ['fields_view' => $fieldsView];

        if ($foreignLocale) {
            $this->connection->update('#__form_lang', $data, ['form_id' => $formId, 'locale' => $locale]);
        } else {
            $this->connection->update('#__form', $data, ['id' => $formId]);
        }
    }

    private function fetchCurrentFields(string $formId, ?string $locale = null): array
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->where('form_id = :form_id')
            ->setParameter('form_id', $formId)
        ;

        if ($locale === null) {
            $qb
                ->from('#__form_field')
            ;
        } else {
            $qb
                ->from('#__form_field_lang')
                ->where('locale = :locale')
                ->setParameter('locale', $locale)
            ;
        }

        return $qb->execute()->fetchAll();
    }
}
