<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class FormRenamed extends DomainEvent
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @param string $formId
     * @param null|string $name
     */
    public function __construct(string $formId, ?string $name)
    {
        parent::__construct($formId);

        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getReceiver(): ?string
    {
        return $this->name;
    }
}
