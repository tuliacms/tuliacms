<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class MessageSubjectChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $subject;

    /**
     * @param string $formId
     * @param null|string $subject
     */
    public function __construct(string $formId, ?string $subject)
    {
        parent::__construct($formId);

        $this->subject = $subject;
    }

    /**
     * @return null|string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }
}
