<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Website\Application\Event\WebsiteCreatedEvent;
use Tulia\Cms\Website\Application\Event\WebsiteDeletedEvent;
use Tulia\Cms\Website\Application\Event\WebsiteEvent;
use Tulia\Cms\Website\Application\Event\WebsiteUpdatedEvent;
use Tulia\Cms\Website\Application\Service\DynamicConfigurationDumper;

/**
 * @author Adam Banaszkiewicz
 */
class DumpDynamicConfiguration implements EventSubscriberInterface
{
    private DynamicConfigurationDumper $dumper;

    public function __construct(DynamicConfigurationDumper $dumper)
    {
        $this->dumper = $dumper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WebsiteUpdatedEvent::class => ['handle', -100],
            WebsiteCreatedEvent::class => ['handle', -100],
            WebsiteDeletedEvent::class => ['handle', -100],
        ];
    }

    public function handle(): void
    {
        $this->dumper->dump();
    }
}
