<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class MessageTemplateChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $template;

    /**
     * @param string $formId
     * @param null|string $template
     */
    public function __construct(string $formId, ?string $template)
    {
        parent::__construct($formId);

        $this->template = $template;
    }

    /**
     * @return null|string
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }
}
