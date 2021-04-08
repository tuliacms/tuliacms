<?php

declare(strict_types=1);

namespace Tulia\Component\CommandBus\Locator;

use Psr\Container\ContainerInterface;
use Tulia\Component\CommandBus\Exception\MissingHandlerException;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerLocator implements LocatorInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var iterable
     */
    protected $handlers = [];

    /**
     * @param iterable $handlers
     */
    public function __construct(ContainerInterface $container, iterable $handlers = [])
    {
        $this->container = $container;
        $this->handlers  = $handlers;
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

        return $this->container->get($this->handlers[$name]);
    }

    /**
     * @param string $command
     * @param string $handler
     */
    public function addHandler(string $command, string $handler): void
    {
        $this->handlers[$command] = $handler;
    }
}
