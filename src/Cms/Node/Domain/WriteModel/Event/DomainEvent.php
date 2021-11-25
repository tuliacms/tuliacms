<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Platform\Domain\WriteModel\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    private string $nodeId;

    private string $nodeType;

    private string $websiteId;

    private string $locale;

    public function __construct(string $nodeId, string $nodeType, string $websiteId, string $locale)
    {
        $this->nodeId = $nodeId;
        $this->nodeType = $nodeType;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public function getNodeType(): string
    {
        return $this->nodeType;
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
