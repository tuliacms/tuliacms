<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use Tulia\Cms\Filemanager\Command\CommandInterface;
use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class CommandRegistry implements CommandRegistryInterface
{
    protected $commands = [];

    /**
     * @param iterable $commands
     */
    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        /** @var CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($command->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): CommandInterface
    {
        /** @var CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($command->getName() === $name) {
                return $command;
            }
        }

        throw new CommandNotFoundException(sprintf('Command %s not found.', $name));
    }
}
