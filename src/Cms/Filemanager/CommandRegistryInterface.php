<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;
use Tulia\Cms\Filemanager\Ports\Domain\Command\CommandInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandRegistryInterface
{
    public function has(string $name): bool;

    /**
     * @throws CommandNotFoundException
     */
    public function get(string $name): CommandInterface;
}
