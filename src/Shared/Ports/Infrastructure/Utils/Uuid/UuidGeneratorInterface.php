<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
interface UuidGeneratorInterface
{
    public function generate(): string;
}
