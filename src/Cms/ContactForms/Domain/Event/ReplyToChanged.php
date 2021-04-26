<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class ReplyToChanged extends DomainEvent
{
    /**
     * @var null|string$email
     */
    private $replyTo;

    /**
     * @param string $formId
     * @param null|string $replyTo
     */
    public function __construct(string $formId, ?string $replyTo)
    {
        parent::__construct($formId);

        $this->replyTo = $replyTo;
    }

    /**
     * @return null|string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }
}
