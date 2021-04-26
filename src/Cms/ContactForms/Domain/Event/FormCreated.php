<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

use Tulia\Cms\ContactForms\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class FormCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $id
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(string $id, string $websiteId, string $locale)
    {
        parent::__construct($id);

        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
