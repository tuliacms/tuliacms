<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\Aggregate;

use Tulia\Cms\Platform\Domain\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AggregateRoot
{
    /**
     * @var array
     */
    private $domainEvents = [];

    /**
     * @param DomainEvent $event
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return array
     */
    public function collectDomainEvents(): array
    {
        $events = $this->domainEvents;

        $this->domainEvents = [];

        return $events;
    }
}
