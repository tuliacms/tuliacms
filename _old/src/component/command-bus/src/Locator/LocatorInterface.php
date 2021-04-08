<?php

declare(strict_types=1);

namespace Tulia\Component\CommandBus\Locator;

use Tulia\Component\CommandBus\Exception\MissingHandlerException;

/**
 * @author Adam Banaszkiewicz
 */
interface LocatorInterface
{
    /**
     * @param object $command
     *
     * @return object
     *
     * @throws MissingHandlerException
     */
    public function locateHandler(object $command): object;
}
