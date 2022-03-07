<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ConsoleWebsiteResolver implements EventSubscriberInterface
{
    protected RegistryInterface $websites;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(RegistryInterface $websites, CurrentWebsiteInterface $currentWebsite)
    {
        $this->websites = $websites;
        $this->currentWebsite = $currentWebsite;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['handle', 9900],
        ];
    }

    public function handle(ConsoleCommandEvent $event): void
    {
        if ($this->currentWebsite->has()) {
            return;
        }

        $input = $event->getInput();

        if ($input->hasOption('website') && $input->getOption('website') !== null) {
            $website = $this->websites->find($input->getOption('website'));
        } elseif ($input->hasArgument('website') && $input->getArgument('website') !== null) {
            $website = $this->websites->find($input->getArgument('website'));
        } else {
            $website = $this->websites[0];
        }

        $this->currentWebsite->set($website);
    }
}
