<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\EventListener;

use Tulia\Cms\Options\Application\Service\OptionsCreator;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;

/**
 * @author Adam Banaszkiewicz
 */
final class CreateOptionsForNewWebsite
{
    /**
     * @var OptionsCreator
     */
    private $optionsCreator;

    public function __construct(OptionsCreator $optionsCreator)
    {
        $this->optionsCreator = $optionsCreator;
    }

    public function handle(WebsiteCreated $event): void
    {
        $this->optionsCreator->createForWebsite($event->getWebsiteId()->getId());
    }
}
