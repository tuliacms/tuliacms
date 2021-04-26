<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector as SymfonyRequestDataCollector;
use Tulia\Framework\Kernel\Event\ControllerEvent;

/**
 * @author Adam Banaszkiewicz
 */
class RequestDataCollector extends SymfonyRequestDataCollector
{
    public function saveControllerFromEvent(ControllerEvent $event): void
    {
        $this->controllers[$event->getRequest()] = $event->getController();
    }
}
