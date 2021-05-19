<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $nodeId;

    private string $websiteId;

    private string $locale;

    public function __construct(string $nodeId, string $websiteId, string $locale)
    {
        $this->nodeId = $nodeId;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
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
