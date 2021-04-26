<?php

declare(strict_types=1);

namespace Tulia\Component\CommandBus\Locator;

use Tulia\Component\CommandBus\Exception\MissingHandlerException;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayLocator implements LocatorInterface
{
    /**
     * @var iterable
     */
    protected $handlers = [];

    /**
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    public function locateHandler(object $command): object
    {
        $name = \get_class($command);

        if (isset($this->handlers[$name]) === false) {
            throw new MissingHandlerException(sprintf('Missing handler for command "%s".', $name));
        }

        return $this->handlers[$name];
    }

    /**
     * @param string $command
     * @param object $handler
     */
    public function addHandler(string $command, object $handler): void
    {
        $this->handlers[$command] = $handler;
    }
}
