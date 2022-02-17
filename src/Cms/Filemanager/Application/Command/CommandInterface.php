<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandInterface
{
    public function getName(): string;

    public function handle(Request $request): array;
}
