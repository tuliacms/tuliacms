<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Tiles;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Dashboard\Tiles\Event\CollectTilesEvent;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getTiles(string $group): array
    {
        $event = new CollectTilesEvent($group);
        $this->dispatcher->dispatch($event);

        return $event->getAll();
    }
}
