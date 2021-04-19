<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Command;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface CommandInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param Request $request
     *
     * @return array
     */
    public function handle(Request $request): array;
}
