<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Exception;

use Exception;

/**
 * @author Adam Banaszkiewicz
 */
class UnknownMigrationVersionException extends Exception
{
    public static function withUnknownVersion(): self
    {
        return new self('Unknown migration version. Please verify this situation.');
    }
}
