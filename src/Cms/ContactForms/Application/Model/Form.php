<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Model;

use Tulia\Cms\ContactForms\Query\Model\Form as QueryModelForm;

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
     * @var string|null
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
     * @var null|string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $subject;

    /**
     * @var null|string
     */
    protected $messageTemplate;

    /**
     * @var null|string
     */
    protected $fieldsTemplate;

    /**
     * @param QueryModelForm $item
     *
     * @return Form
     */
    public static function fromQueryModel(QueryModelForm $item): self
    {
        $self = new self($item->getId(), $item->getName());
        $self->setId($item->getId());
        $self->setWebsiteId($item->getWebsiteId());
        $self->setReceivers($item->getReceivers());
        $self->setSenderEmail($item->getSenderEmail());
        $self->setSenderName($item->getSenderName());
        $self->setReplyTo($item->getReplyTo());
        $self->setLocale($item->getLocale());
        $self->setName($item->getName());
        $self->setSubject($item->getSubject());
        $self->setMessageTemplate($item->getMessageTemplate());
        $self->setFieldsTemplate($item->getFieldsTemplate());

        return $self;
    }

    public function __construct(string $id, ?string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
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
     * @return null|string
     */
    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    /**
     * @param null|string $senderEmail
     */
    public function setSenderEmail(?string $senderEmail): void
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
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param null|string $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return null|string
     */
    public function getMessageTemplate(): ?string
    {
        return $this->messageTemplate;
    }

    /**
     * @param null|string $messageTemplate
     */
    public function setMessageTemplate(?string $messageTemplate): void
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
}
