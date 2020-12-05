<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Aggregate;

use Tulia\Cms\ContactForms\Domain\Exception\DomainException;
use Tulia\Cms\ContactForms\Domain\Exception\FieldsTemplatePolicyException;
use Tulia\Cms\ContactForms\Domain\Exception\InvalidSenderEmailException;
use Tulia\Cms\ContactForms\Domain\Policy\FieldsTemplatePolicyInterface;
use Tulia\Cms\ContactForms\Domain\ValueObject\AggregateId;
use Tulia\Cms\ContactForms\Domain\Event;
use Tulia\Cms\ContactForms\Domain\ValueObject\ReplyTo;
use Tulia\Cms\ContactForms\Domain\ValueObject\Sender;
use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
final class Form extends AggregateRoot
{
    /**
     * @var AggregateId
     */
    private $id;

    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $receivers = [];

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var ReplyTo
     */
    private $replyTo;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var null|string
     */
    private $subject;

    /**
     * @var null|string
     */
    private $fieldsTemplate;

    /**
     * @var null|string
     */
    private $messageTemplate;

    /**
     * @param AggregateId $id
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(AggregateId $id, string $websiteId, string $locale)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
        $this->locale = $locale;

        $this->recordThat(new Event\FormCreated($id->getId(), $websiteId, $locale));
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @return Sender
     */
    public function getSender(): Sender
    {
        return $this->sender;
    }

    /**
     * @return ReplyTo
     */
    public function getReplyTo(): ReplyTo
    {
        return $this->replyTo;
    }

    /**
     * @param array $receivers
     */
    public function changeReceivers(array $receivers): void
    {
        if (empty($receivers)) {
            foreach ($this->receivers as $receiver) {
                $this->recordThat(new Event\ReceiverRemoved($this->id->getId(), $receiver));
            }

            return;
        }

        $receivers = array_map('trim', $receivers);
        $receivers = array_filter($receivers, function ($val) {
            return filter_var($val, FILTER_VALIDATE_EMAIL) ? $val : null;
        });

        $toUpdate = array_intersect($this->receivers, $receivers);
        $toAdd = array_diff($receivers, $toUpdate);
        $toRemove = array_diff($this->receivers, $toUpdate);

        $this->receivers = $receivers;

        foreach ($toAdd as $receiver) {
            $this->recordThat(new Event\ReceiverAdded($this->id->getId(), $receiver));
        }
        foreach ($toRemove as $receiver) {
            $this->recordThat(new Event\ReceiverRemoved($this->id->getId(), $receiver));
        }
    }

    /*public function changeFields(array $fields): void
    {
        if (empty($fields)) {
            foreach ($this->fields as $field) {
                $this->recordThat(new Event\FieldRemoved($this->id->getId(), $field->getName()));
            }

            $this->fields->empty();

            return;
        }

        $new = array_keys($fields);
        $old = $this->fields->keys();

        $toUpdate = array_intersect($old, $new);
        $toAdd    = array_diff($new, $toUpdate);
        $toRemove = array_diff($old, $toUpdate);

        foreach ($toAdd as $name) {
            $this->fields[$name] = new Field(
                $fields[$name]['name'],
                $fields[$name]['type'],
                $fields[$name]['options']
            );

            $this->recordThat(new Event\FieldAdded($this->id->getId(), $name));
        }

        foreach ($toRemove as $name) {
            unset($this->fields[$name]);

            $this->recordThat(new Event\FieldRemoved($this->id->getId(), $name));
        }

        foreach ($toUpdate as $name) {
            $this->fields[$name] = new Field(
                $fields[$name]['name'],
                $fields[$name]['type'],
                $fields[$name]['options']
            );

            $this->recordThat(new Event\FieldChanged($this->id->getId(), $name));
        }
    }*/

    /**
     * @param string $email
     * @param string|null $name
     *
     * @throws InvalidSenderEmailException
     */
    public function changeSender(string $email, ?string $name): void
    {
        if ($this->sender === null || $this->sender->getEmail() !== $email || $this->sender->getName() !== $name) {
            $this->sender = new Sender($email, $name);

            $this->recordThat(new Event\SenderChanged($this->id->getId(), $email, $name));
        }
    }

    /**
     * @param null|string $email
     *
     * @throws InvalidSenderEmailException
     */
    public function replyTo(?string $email): void
    {
        if ($this->replyTo === null || $this->replyTo->getEmail() !== $email) {
            $this->replyTo = new ReplyTo($email);

            $this->recordThat(new Event\ReplyToChanged($this->id->getId(), $email));
        }
    }

    /**
     * @param null|string $name
     */
    public function rename(?string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;

            $this->recordThat(new Event\FormRenamed($this->id->getId(), $name));
        }
    }

    /**
     * @param null|string $subject
     */
    public function setMessageSubject(?string $subject): void
    {
        if ($this->subject !== $subject) {
            $this->subject = $subject;

            $this->recordThat(new Event\MessageSubjectChanged($this->id->getId(), $subject));
        }
    }

    /**
     * @param string|null $fieldsTemplate
     *
     * @throws DomainException
     */
    public function changeFieldsTemplate(FieldsTemplatePolicyInterface $policy, ?string $fieldsTemplate): void
    {
        if ($this->fieldsTemplate !== $fieldsTemplate) {
            if ($policy->templateCanBeApplied($fieldsTemplate) === false) {
                throw new FieldsTemplatePolicyException('Cannot change the fields template because policy requirements are not met.');
            }

            $this->fieldsTemplate = $fieldsTemplate;

            $this->recordThat(new Event\FieldsTemplateChanged($this->id->getId(), $this->locale, $fieldsTemplate));
        }
    }

    /**
     * @param string|null $template
     */
    public function changeMessageTemplate(?string $template): void
    {
        if ($this->messageTemplate !== $template) {
            $this->messageTemplate = $template;

            $this->recordThat(new Event\MessageTemplateChanged($this->id->getId(), $template));
        }
    }
}
