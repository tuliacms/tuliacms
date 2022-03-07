<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileReader;

/**
 * @author Adam Banaszkiewicz
 */
class JsonFileReader implements FileReaderInterface
{
    public function supports(string $filepath): bool
    {
        return pathinfo($filepath, PATHINFO_EXTENSION) === 'json';
    }

    public function read(string $filepath): array
    {
        return json_decode(file_get_contents($filepath), true, JSON_THROW_ON_ERROR);
    }
}
