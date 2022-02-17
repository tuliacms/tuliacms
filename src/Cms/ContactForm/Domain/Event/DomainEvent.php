<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Event;

use Tulia\Cms\Shared\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $formId;

    public function __construct(string $formId)
    {
        $this->formId = $formId;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }
}
