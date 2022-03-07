<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class FileNotSupportedException extends AbstractImporterException
{
    public static function fromFilepath(string $filepath): self
    {
        return new self(sprintf('File %s with it\'s format is not supported.', pathinfo($filepath, PATHINFO_FILENAME)));
    }
}
