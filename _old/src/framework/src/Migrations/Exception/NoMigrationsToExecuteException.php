<?php

declare(strict_types=1);

namespace Tulia\Framework\Migrations\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class NoMigrationsToExecuteException extends MigrationException
{
    public static function withVersion(string $version): self
    {
        return new self(sprintf(
            'The version "%s" couldn\'t be reached, there are no registered migrations.',
            $version
        ));
    }
}
