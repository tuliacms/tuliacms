<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandPropagatorInterface
{
    /**
     * @param string $cmd
     * @param Request $request
     *
     * @return array
     *
     * @throws CommandNotFoundException
     */
    public function handle(string $cmd, Request $request): array;
}
