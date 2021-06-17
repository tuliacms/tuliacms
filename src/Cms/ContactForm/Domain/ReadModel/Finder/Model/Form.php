<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\ReadModel\Finder\Model;

use InvalidArgumentException;

/**
 * @author Adam Banaszkiewicz
 */
class Form
{
    private string $id;

    private string $websiteId;

    private array $receivers = [];

    private string $senderEmail;

    private ?string $senderName = null;

    private ?string $replyTo = null;

    private string $locale;

    private string $name;

    private string $subject;

    private ?string $messageTemplate = null;

    private ?string $fieldsTemplate = null;

    private ?string $fieldsView = null;

    private array $fields = [];

    private bool $translated = false;

    public static function buildFromArray(array $data): self
    {
        $form = new self();

        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Form ID must be provided.');
        }

        if (isset($data['website_id']) === false) {
            throw new InvalidArgumentException('Form website_id must be provided.');
        }

        if (isset($data['locale']) === false) {
            $data['locale'] = 'en_US';
        }

        $form->setId($data['id']);
        $form->setWebsiteId($data['website_id']);
        $form->setLocale($data['locale']);
        $form->setTranslated((bool) ($data['translated'] ?? false));
        $form->setReceivers($data['receivers'] ?? []);
        $form->setSenderEmail($data['sender_email'] ?? '');
        $form->setSenderName($data['sender_name'] ?? null);
        $form->setReplyTo($data['reply_to'] ?? null);
        $form->setName($data['name'] ?? '');
        $form->setSubject($data['subject'] ?? '');
        $form->setMessageTemplate($data['message_template'] ?? '');
        $form->setFieldsTemplate($data['fields_template'] ?? '');
        $form->setFieldsView($data['fields_view'] ?? null);
        $form->setFields($data['fields'] ?? []);

        return $form;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getReceivers(): array
    {
        return $this->receivers;
    }

    public function setReceivers(array $receivers): void
    {
        $this->receivers = $receivers;
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    public function setReplyTo(?string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
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

    public function getMessageTemplate(): ?string
    {
        return $this->messageTemplate;
    }

    public function setMessageTemplate(?string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }

    public function getFieldsTemplate(): ?string
    {
        return $this->fieldsTemplate;
    }

    public function setFieldsTemplate(?string $fieldsTemplate): void
    {
        $this->fieldsTemplate = $fieldsTemplate;
    }

    public function getFieldsView(): ?string
    {
        return $this->fieldsView;
    }

    public function setFieldsView(?string $fieldsView): void
    {
        $this->fieldsView = $fieldsView;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getTranslated(): bool
    {
        return $this->translated;
    }

    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }
}
