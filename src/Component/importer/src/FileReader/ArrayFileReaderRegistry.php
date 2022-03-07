<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileReader;

use Tulia\Component\Importer\Exception\FileNotSupportedException;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayFileReaderRegistry implements FileReaderRegistryInterface
{
    /** @var FileReaderInterface[] */
    private array $readers = [];

    public function addReader(FileReaderInterface $reader)
    {
        $this->readers[] = $reader;
    }

    public function all(): array
    {
        return $this->readers;
    }

    public function getSupportingReader(string $filepath): FileReaderInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->supports($filepath)) {
                return $reader;
            }
        }

        throw FileNotSupportedException::fromFilepath($filepath);
    }
}
