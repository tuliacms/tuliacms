<?php

declare(strict_types=1);

namespace Tulia\Component\CommandBus;

use Tulia\Component\CommandBus\Exception\MissingHandlerException;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandBusInterface
{
    /**
     * @param object $command
     *
     * @throws MissingHandlerException
     */
    public function handle(object $command): void;
}
