<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $websiteId;

    public function __construct(string $websiteId)
    {
        $this->websiteId = $websiteId;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }
}
