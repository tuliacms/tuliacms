<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\Aggregate;

use Tulia\Cms\Platform\Domain\Event\DomainEvent;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AggregateRoot
{
    private array $domainEvents = [];

    protected function recordThat(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function collectDomainEvents(): array
    {
        $events = $this->domainEvents;

        $this->domainEvents = [];

        return $events;
    }
}
