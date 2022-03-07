<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\FileReader;

use Tulia\Component\Importer\Exception\FileNotSupportedException;

/**
 * @author Adam Banaszkiewicz
 */
interface FileReaderRegistryInterface
{
    public function addReader(FileReaderInterface $reader);

    /**
     * @return FileReaderInterface[]
     */
    public function all(): array;

    /**
     * @throws FileNotSupportedException
     */
    public function getSupportingReader(string $filepath): FileReaderInterface;
}
