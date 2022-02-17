<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use RuntimeException;

/**
 * @author Adam Banaszkiewicz
 */
class CommandRegistry
{
    protected $commands = [];

    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
    }

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
     * @throws RuntimeException
     */
    public function get(string $name): CommandInterface
    {
        /** @var CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($command->getName() === $name) {
                return $command;
            }
        }

        throw new RuntimeException(sprintf('Command %s not found.', $name));
    }
}
