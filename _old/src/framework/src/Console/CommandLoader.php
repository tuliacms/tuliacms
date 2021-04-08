<?php

declare(strict_types=1);

namespace Tulia\Framework\Console;

use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Tulia\Component\DependencyInjection\LazyServiceIterator;

/**
 * @author Adam Banaszkiewicz
 */
class CommandLoader implements CommandLoaderInterface
{
    /**
     * @var array|iterable|Command[]|LazyServiceIterator
     */
    protected $commands;

    /**
     * @var array|Command[]
     */
    protected $prepared = [];

    /**
     * @param iterable $commands
     */
    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
    }

    public function get(string $name): Command
    {
        $this->prepareCommands();

        return $this->prepared[$name];
    }

    public function has(string $name): bool
    {
        $this->prepareCommands();

        return isset($this->prepared[$name]);
    }

    public function getNames(): array
    {
        $this->prepareCommands();

        return array_keys($this->prepared);
    }

    protected function prepareCommands(): void
    {
        if ($this->prepared !== []) {
            return;
        }

        foreach ($this->commands as $name => $command) {
            $params = $this->commands->getParameters($name);

           foreach ($params as $item) {
               $this->prepared[$item['command']] = $command;
           }
        }
    }
}
