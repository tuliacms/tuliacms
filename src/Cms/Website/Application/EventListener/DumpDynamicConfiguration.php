<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Website\Application\Service\DynamicConfigurationDumper;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;

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
            WebsiteUpdated::class => ['handle', -100],
            WebsiteCreated::class => ['handle', -100],
            WebsiteDeleted::class => ['handle', -100],
        ];
    }

    public function handle(): void
    {
        $this->dumper->dump();
    }
}
