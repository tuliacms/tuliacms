<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTemplateChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @param string $formId
     * @param string $locale
     * @param string|null $template
     */
    public function __construct(string $formId, string $locale, ?string $template)
    {
        parent::__construct($formId);

        $this->locale = $locale;
        $this->template = $template;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
