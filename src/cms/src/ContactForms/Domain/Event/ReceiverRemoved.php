<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class ReceiverRemoved extends DomainEvent
{
    /**
     * @var string
     */
    private $receiver;

    /**
     * @param string $formId
     * @param string $receiver
     */
    public function __construct(string $formId, string $receiver)
    {
        parent::__construct($formId);

        $this->receiver = $receiver;
    }

    /**
     * @return string
     */
    public function getReceiver(): string
    {
        return $this->receiver;
    }
}
