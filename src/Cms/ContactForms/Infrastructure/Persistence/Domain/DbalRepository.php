<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain;

use Tulia\Cms\ContactForms\Domain\Aggregate\Field;
use Tulia\Cms\ContactForms\Domain\Aggregate\FieldCollection;
use Tulia\Cms\ContactForms\Domain\Aggregate\FieldsTemplate;
use Tulia\Cms\ContactForms\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForms\Domain\ValueObject\AggregateId;
use Tulia\Cms\ContactForms\Domain\Aggregate\Form;
use Tulia\Cms\ContactForms\Domain\RepositoryInterface;
use Tulia\Cms\ContactForms\Domain\ValueObject\ReplyTo;
use Tulia\Cms\ContactForms\Domain\ValueObject\Sender;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var DbalFormPersister
     */
    protected $formPersister;

    /**
     * @var DbalFieldPersister
     */
    protected $fieldPersister;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param ConnectionInterface $connection
     * @param DbalFormPersister $formPersister
     * @param DbalFieldPersister $fieldPersister
     * @param HydratorInterface $hydrator
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        ConnectionInterface $connection,
        DbalFormPersister $formPersister,
        DbalFieldPersister $fieldPersister,
        HydratorInterface $hydrator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection = $connection;
        $this->fieldPersister = $fieldPersister;
        $this->formPersister = $formPersister;
        $this->hydrator = $hydrator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id, string $locale): Form
    {
        $form = $this->connection->fetchAll('
            SELECT
                tm.*,
                COALESCE(tl.locale, :defaultLocale) AS locale,
                COALESCE(tl.name, tm.name) AS name,
                COALESCE(tl.subject, tm.subject) AS subject,
                COALESCE(tl.message_template, tm.message_template) AS message_template,
                COALESCE(tl.fields_template, tm.fields_template) AS fields_template
            FROM #__form AS tm
            LEFT JOIN #__form_lang AS tl
                ON tm.id = tl.form_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'id'     => $id->getId(),
            'locale' => $locale,
            'defaultLocale' => $this->currentWebsite->getLocale()->getCode(),
        ]);

        if (empty($form)) {
            throw new FormNotFoundException();
        }

        $fields = $this->connection->fetchAll('
            SELECT *
            FROM #__form_field AS tm
            INNER JOIN #__form_field_lang AS tl
                ON tl.form_id = :form_id AND tl.name = tm.name AND tl.locale = :locale
            WHERE tm.form_id = :form_id', [
            'form_id' => $id->getId(),
            'locale' => $locale,
        ]);

        $form = reset($form);

        /** @var Form $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'               => new AggregateId($form['id']),
            'fields'           => $this->hydrateFields($fields),
            'locale'           => $form['locale'],
            'websiteId'        => $form['website_id'],
            'subject'          => $form['subject'],
            'name'             => $form['name'],
            'messageTemplate'  => $form['message_template'],
            'fieldsTemplate'   => $form['fields_template'],
            'sender'           => new Sender($form['sender_email'], $form['sender_name']),
            'replyTo'          => new ReplyTo($form['reply_to']),
            'receivers'        => json_decode($form['receivers'], true),
        ], Form::class);

        return $aggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Form $form): void
    {
        $data = $this->extract($form);

        $this->connection->transactional(function () use ($data) {
            $defaultLocale = $this->currentWebsite->getDefaultLocale()->getCode();

            $this->formPersister->save(
                $data,
                $defaultLocale
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Form $form): void
    {
        $data = $this->extract($form);

        $this->connection->transactional(function () use ($data) {
            $this->formPersister->delete($data['id']);
            $this->fieldPersister->delete($data['id']);
        });
    }

    private function extract(Form $form): array
    {
        $data = $this->hydrator->extract($form);
        $data['id'] = $form->getId()->getId();

        $data['replyTo']     = $form->getReplyTo()->getEmail();
        $data['senderName']  = $form->getSender()->getName();
        $data['senderEmail'] = $form->getSender()->getEmail();

        unset($data['sender']);

        if (empty($data['locale'])) {
            $data['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        if (empty($data['websiteId'])) {
            $data['websiteId'] = $this->currentWebsite->getId();
        }

        return $data;
    }

    private function hydrateFields(array $fields): FieldCollection
    {
        $collection = new FieldCollection();

        foreach ($fields as $field)
        {
            $collection[$field['name']] = new Field(
                $field['name'],
                $field['type'],
                json_decode($field['options'], true)
            );
        }

        return $collection;
    }
}
