<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class FormCreated extends DomainEvent
{
    private string $websiteId;

    private string $locale;

    public function __construct(string $id, string $websiteId, string $locale)
    {
        parent::__construct($id);

        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
