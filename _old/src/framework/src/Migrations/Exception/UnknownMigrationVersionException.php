<?php

declare(strict_types=1);

namespace Tulia\Framework\Migrations\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class UnknownMigrationVersionException extends MigrationException
{
    public static function withVersion(string $version): self
    {
        return new self(sprintf(
            'Unknown version: %s',
            $version
        ));
    }
}
