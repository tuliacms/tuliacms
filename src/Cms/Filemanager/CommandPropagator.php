<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class CommandPropagator implements CommandPropagatorInterface
{
    /**
     * @var CommandRegistryInterface
     */
    protected $commands;

    /**
     * @param CommandRegistryInterface $commands
     */
    public function __construct(CommandRegistryInterface $commands)
    {
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(string $cmd, Request $request): array
    {
        if ($this->commands->has($cmd) === false) {
            throw new CommandNotFoundException(sprintf('Command %s not found.', $cmd));
        }

        return $this->commands->get($cmd)->handle($request);
    }
}
