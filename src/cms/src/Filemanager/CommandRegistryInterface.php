<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use Tulia\Cms\Filemanager\Command\CommandInterface;
use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandRegistryInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     *
     * @return CommandInterface
     *
     * @throws CommandNotFoundException
     */
    public function get(string $name): CommandInterface;
}
