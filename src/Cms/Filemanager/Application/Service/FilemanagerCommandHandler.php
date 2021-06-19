<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use RuntimeException;
use Tulia\Cms\Filemanager\Application\Command\CommandRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerCommandHandler
{
    protected CommandRegistry $commands;

    public function __construct(CommandRegistry $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @throws RuntimeException
     */
    public function handle(string $cmd, Request $request): array
    {
        if ($this->commands->has($cmd) === false) {
            throw new RuntimeException(sprintf('Filemanager command "%s" not found.', $cmd));
        }

        return $this->commands->get($cmd)->handle($request);
    }
}
