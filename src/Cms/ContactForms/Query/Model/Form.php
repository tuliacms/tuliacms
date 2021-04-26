<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query\Model;

use Exception;
use InvalidArgumentException;

/**
 * @author Adam Banaszkiewicz
 */
class Form
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var array
     */
    protected $receivers = [];

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var string|null
     */
    protected $senderName;

    /**
     * @var string|null
     */
    protected $replyTo;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $messageTemplate;

    /**
     * @var null|string
     */
    protected $fieldsTemplate;

    /**
     * @var null|string
     */
    protected $fieldsView;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $translated = false;

    /**
     * @var string[]
     */
    protected static $fieldsList = [
        'id'               => 'id',
        'websiteId'        => 'websiteId',
        'receivers'        => 'receivers',
        'sender_name'      => 'senderName',
        'sender_email'     => 'senderEmail',
        'reply_to'         => 'replyTo',
        'locale'           => 'locale',
        'name'             => 'name',
        'subject'          => 'subject',
        'message_template' => 'messageTemplate',
        'fields_template'  => 'fieldsTemplate',
        'fields_view'      => 'fieldsView',
        'fields'           => 'fields',
        'translated'       => 'translated',
    ];

    /**
     * @param array $data
     *
     * @return Form
     *
     * @throws Exception
     */
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

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $params = []): array
    {
        $params = array_merge([
            'skip' => [],
        ], $params);

        $result = [];

        foreach (static::$fieldsList as $key => $property) {
            $result[$key] = $this->{$property};
        }

        foreach ($params['skip'] as $skip) {
            unset($result[$skip]);
        }

        return $result;
    }

    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @param string $websiteId
     */
    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return array
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }

    /**
     * @param array $receivers
     */
    public function setReceivers(array $receivers): void
    {
        $this->receivers = $receivers;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return string|null
     */
    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    /**
     * @param string|null $senderName
     */
    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string|null
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @param string|null $replyTo
     */
    public function setReplyTo(?string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getMessageTemplate(): string
    {
        return $this->messageTemplate;
    }

    /**
     * @param string $messageTemplate
     */
    public function setMessageTemplate(string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }

    /**
     * @return string|null
     */
    public function getFieldsTemplate(): ?string
    {
        return $this->fieldsTemplate;
    }

    /**
     * @param string|null $fieldsTemplate
     */
    public function setFieldsTemplate(?string $fieldsTemplate): void
    {
        $this->fieldsTemplate = $fieldsTemplate;
    }

    /**
     * @return string|null
     */
    public function getFieldsView(): ?string
    {
        return $this->fieldsView;
    }

    /**
     * @param string|null $fieldsView
     */
    public function setFieldsView(?string $fieldsView): void
    {
        $this->fieldsView = $fieldsView;
    }

    /**
     * @return bool
     */
    public function isTranslated(): bool
    {
        return $this->translated;
    }

    /**
     * @param bool $translated
     */
    public function setTranslated(bool $translated): void
    {
        $this->translated = $translated;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
