<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Bus\Event;

/**
 * @author Adam Banaszkiewicz
 */
interface EventBusInterface
{
    public function dispatchCollection(array $events): void;

    public function dispatch(object $event): void;
}
