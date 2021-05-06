<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;

/**
 * @author Adam Banaszkiewicz
 */
final class RemoveOptionsForRemovedWebsite implements EventSubscriberInterface
{
    private WebsitesOptionsRegistrator $websitesOptionsRegistrator;

    public function __construct(WebsitesOptionsRegistrator $websitesOptionsRegistrator)
    {
        $this->websitesOptionsRegistrator = $websitesOptionsRegistrator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WebsiteDeleted::class => ['handle', 0],
        ];
    }

    public function handle(WebsiteDeleted $event): void
    {
        $this->websitesOptionsRegistrator->removeOptionsForWebsite($event->getWebsiteId());
    }
}
