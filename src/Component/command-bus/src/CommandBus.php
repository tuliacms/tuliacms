<?php

declare(strict_types=1);

namespace Tulia\Component\CommandBus;

use Tulia\Component\CommandBus\Locator\LocatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CommandBus implements CommandBusInterface
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(object $command): void
    {
        $handler = $this->locator->locateHandler($command);
        $handler->handle($command);
    }
}
