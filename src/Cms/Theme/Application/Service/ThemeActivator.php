<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\Service;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Theme\Application\Exception\ThemeNotFoundException;
use Tulia\Cms\Theme\Domain\Event\ThemeActivated;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeActivator
{
    private ManagerInterface $manager;
    private ActivatorInterface $activator;
    private EventDispatcherInterface $eventDispatcher;
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        ManagerInterface $manager,
        ActivatorInterface $activator,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->manager = $manager;
        $this->activator = $activator;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * @param string $name
     * @throws ThemeNotFoundException|MissingThemeException
     */
    public function activateTheme(string $name): void
    {
        $theme = $this->manager->getStorage()->get($name);

        if (! $theme) {
            throw ThemeNotFoundException::withName($name);
        }

        $this->activator->activate($theme->getName());

        $this->eventDispatcher->dispatch(new ThemeActivated($name, $this->currentWebsite->getId()));
    }
}
