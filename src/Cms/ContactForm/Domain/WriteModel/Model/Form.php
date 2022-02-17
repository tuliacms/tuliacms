<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\Model;

use Tulia\Cms\ContactForm\Domain\Event;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\ValueObject\FormId;
use Tulia\Cms\Platform\Domain\WriteModel\EntitiesChangelog;
use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class Form extends AggregateRoot
{
    private FormId $id;

    private string $websiteId;

    private string $locale;

    private array $receivers = [];

    private string $senderName = '';

    private string $senderEmail = '';

    private string $replyTo = '';

    private string $name = '';

    private string $subject = '';

    private ?string $fieldsTemplate = null;

    private ?string $fieldsView = null;

    private array $fields = [];

    private ?string $messageTemplate = null;

    private bool $translated = true;

    private EntitiesChangelog $fieldsChangeLog;

    private function __construct(string $id, string $websiteId, string $locale)
    {
        $this->id = new FormId($id);
        $this->websiteId = $websiteId;
        $this->locale = $locale;

        $this->fieldsChangeLog = new EntitiesChangelog();
    }

    public static function createNew(string $id, string $websiteId, string $locale): self
    {
        $self = new self($id, $websiteId, $locale);
        $self->setMessageTemplate('{{ contact_form_fields() }}');
        $self->recordThat(new Event\FormCreated($id, $websiteId, $locale));

        return $self;
    }

    public static function buildFromArray(array $data): self
    {
        $self = new self($data['id'], $data['website_id'], $data['locale']);
        $self->setReceivers($data['receivers'] ?? []);
        $self->setSenderName($data['sender_name'] ?? '');
        $self->setSenderEmail($data['sender_email'] ?? '');
        $self->setReplyTo($data['reply_to'] ?? '');
        $self->setName($data['name'] ?? '');
        $self->subject = $data['subject'] ?? '';
        $self->translated = (bool) ($data['translated'] ?? true);
        $self->messageTemplate = $data['message_template'] ?? '';
        $self->fieldsTemplate = $data['fields_template'] ?? '';

        foreach ($data['fields'] ?? [] as $field) {
            $self->fields[$field['name']] = Field::buildFromArray($field);
        }

        return $self;
    }

    public function getId(): FormId
    {
        return $this->id;
    }

    public function setId(FormId $id): void
    {
        $this->id = $id;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getReceivers(): array
    {
        return $this->receivers;
    }

    public function setReceivers(array $receivers): void
    {
        if (empty($receivers)) {
            $this->receivers = [];
            return;
        }

        $receivers = array_map('trim', $receivers);
        $receivers = array_filter($receivers, function ($val) {
            return filter_var($val, FILTER_VALIDATE_EMAIL) ? $val : null;
        });

        $this->receivers = $receivers;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function setReplyTo(string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @throws InvalidFieldNameException
     * @throws MultipleFieldsInTemplateException
     */
    public function setFieldsTemplate(
        array $fields,
        ?string $fieldsTemplate,
        FieldsParserInterface $fieldsParser
    ): void {
        $fields = $this->validateFieldsNames($fields);

        $stream = $fieldsParser->parse((string) $fieldsTemplate, $fields);
        $newFields = $stream->allFields();

        $changelog = $this->calculateFieldsChangelog(
            array_keys($newFields),
            array_keys($this->fields)
        );

        $this->fieldsTemplate = $fieldsTemplate;
        $this->fieldsView = $stream->getResult();

        foreach ($changelog['removed'] as $name) {
            $this->fieldsChangeLog->recordEntityChange('remove', $this->fields[$name]);
            unset($this->fields[$name]);
        }

        foreach ($changelog['added'] as $name) {
            $this->fields[$name] = Field::buildFromArray($newFields[$name]);
            $this->fieldsChangeLog->recordEntityChange('add', $this->fields[$name]);
        }

        foreach ($changelog['updated'] as $name) {
            $this->fields[$name] = Field::buildFromArray($newFields[$name]);
            $this->fieldsChangeLog->recordEntityChange('update', $this->fields[$name]);
        }
    }

    public function getFieldsTemplate(): ?string
    {
        return $this->fieldsTemplate;
    }

    public function getFieldsView(): ?string
    {
        return $this->fieldsView;
    }

    public function getMessageTemplate(): ?string
    {
        return $this->messageTemplate;
    }

    public function setMessageTemplate(?string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }

    /**
     * @return Field[]
     */
    public function fields(): iterable
    {
        foreach ($this->fields as $field) {
            yield $field;
        }
    }

    public function getFieldsChanges(): array
    {
        return $this->fieldsChangeLog->collectEntitiesChanges();
    }

    public function isTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }

    private function calculateFieldsChangelog(array $newFields, array $oldFields): array
    {
        $toUpdate = array_intersect($newFields, $oldFields);
        $toAdd    = array_diff($newFields, $toUpdate);
        $toRemove = array_diff($oldFields, $toUpdate);

        $log['added'] = $toAdd;
        $log['removed'] = $toRemove;
        $log['updated'] = $toUpdate;

        return $log;
    }

    private function validateFieldsNames(array $fields): array
    {
        foreach ($fields as $key => $field) {
            $name = strtolower($field['name']);
            $name = preg_replace('/[^a-z0-9_]+/i', '_', $name);

            $fields[$key]['name'] = substr($name, 0, 32);
        }

        return $fields;
    }
}
