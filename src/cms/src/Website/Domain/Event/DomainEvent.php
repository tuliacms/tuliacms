<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\Event\DomainEvent as PlatformDomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class DomainEvent extends PlatformDomainEvent
{
    /**
     * @var AggregateId
     */
    private $websiteId;

    public function __construct(AggregateId $websiteId)
    {
        $this->websiteId = $websiteId;
    }

    public function getWebsiteId(): AggregateId
    {
        return $this->websiteId;
    }
}
