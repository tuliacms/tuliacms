<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var string
     */
    private $formId;

    /**
     * @param string $formId
     */
    public function __construct(string $formId)
    {
        $this->formId = $formId;
    }

    /**
     * @return string
     */
    public function getFormId(): string
    {
        return $this->formId;
    }
}
