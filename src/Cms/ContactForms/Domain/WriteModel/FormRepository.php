<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\WriteModel;

use Tulia\Cms\ContactForms\Domain\Event\FormDeleted;
use Tulia\Cms\ContactForms\Domain\WriteModel\Model\Field;
use Tulia\Cms\ContactForms\Domain\WriteModel\Model\Form;
use Tulia\Cms\ContactForms\Ports\Infrastructure\Persistence\Domain\WriteModel\ContactFormWriteStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormRepository
{
    private CurrentWebsiteInterface $currentWebsite;

    private UuidGeneratorInterface $uuidGenerator;

    private ContactFormWriteStorageInterface $storage;

    private EventBusInterface $eventBus;

    public function __construct(
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator,
        ContactFormWriteStorageInterface $storage,
        EventBusInterface $eventBus
    ) {
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
        $this->storage = $storage;
        $this->eventBus = $eventBus;
    }

    public function createNew(): Form
    {
        return Form::createNew(
            $this->uuidGenerator->generate(),
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode(),
        );
    }

    public function createNewField(array $data): Field
    {
        return Field::buildFromArray($data);
    }

    /**
     * @return Field[]
     */
    public function createNewFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            $result[$field['name']] = $this->createNewField($field);
        }

        return $result;
    }

    public function find(string $id): Form
    {
        $form = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        $form['receivers'] = json_decode($form['receivers'], true);
        $form['fields'] = array_map(function ($field) {
            $field['options'] = json_decode($field['options'], true);
            return $field;
        }, $form['fields']);

        return Form::buildFromArray($form);
    }

    public function insert(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->insert($this->extract($form), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($form->collectDomainEvents());
    }

    public function update(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->update($this->extract($form), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($form->collectDomainEvents());
    }

    public function delete(Form $form): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->delete($this->extract($form));
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(FormDeleted::fromForm($form));
    }

    private function extract(Form $form): array
    {
        $result = [
            'id' => $form->getId(),
            'website_id' => $form->getWebsiteId(),
            'locale' => $form->getLocale(),
            'receivers' => json_encode($form->getReceivers()),
            'sender_name' => $form->getSenderName(),
            'sender_email' => $form->getSenderEmail(),
            'reply_to' => $form->getReplyTo(),
            'name' => $form->getName(),
            'subject' => $form->getSubject(),
            'fields_template' => $form->getFieldsTemplate(),
            'fields_view' => $form->getFieldsView(),
            'message_template' => $form->getMessageTemplate(),
            'fields' => [],
        ];

        $itemsChanges = $form->getFieldsChanges();

        foreach ($itemsChanges as $changeData) {
            /** @var Field $field */
            $field = $changeData['entity'];

            $result['fields'][] = [
                '_change_type' => $changeData['type'],
                'id' => $field->getName(),
                'form_id' => $form->getId(),
                'locale' => $form->getLocale(),
                'name' => $field->getName(),
                'type' => $field->getType(),
                'type_alias' => $field->getTypeAlias(),
                'options' => json_encode($field->getOptions()),
            ];
        }

        return $result;
    }
}
