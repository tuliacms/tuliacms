<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Uuid;

/**
 * @author Adam Banaszkiewicz
 */
interface UuidGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
