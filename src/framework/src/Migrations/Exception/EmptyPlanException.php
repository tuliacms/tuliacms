<?php

declare(strict_types=1);

namespace Tulia\Framework\Migrations\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class EmptyPlanException extends MigrationException
{
    public static function withEmpty(): self
    {
        return new self('Empty migration plan. No migrations to execute.');
    }
}
