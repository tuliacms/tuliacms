<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileReader;

/**
 * @author Adam Banaszkiewicz
 */
interface FileReaderInterface
{
    public function supports(string $filepath): bool;

    public function read(string $filepath): array;
}
