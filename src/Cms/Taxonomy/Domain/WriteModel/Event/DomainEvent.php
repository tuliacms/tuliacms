<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Platform\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $termId;

    private string $type;

    private string $websiteId;

    private string $locale;

    public function __construct(string $termId, string $type, string $websiteId, string $locale)
    {
        $this->termId = $termId;
        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public function getTermId(): string
    {
        return $this->termId;
    }

    public function getType(): string
    {
        return $this->type;
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
