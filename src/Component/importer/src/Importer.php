<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileReader\FileReaderRegistryInterface;
use Tulia\Component\Importer\Validation\SchemaValidatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Importer implements ImporterInterface
{
    private FileReaderRegistryInterface $fileReaderRegistry;
    private SchemaValidatorInterface $schemaValidator;

    public function __construct(
        FileReaderRegistryInterface $fileReaderRegistry,
        SchemaValidatorInterface $schemaValidator
    ) {
        $this->fileReaderRegistry = $fileReaderRegistry;
        $this->schemaValidator = $schemaValidator;
    }

    /**
     * @throws Exception\FileNotSupportedException
     */
    public function importFromFile(string $filepath, ?string $realFilename = null): void
    {
        $data = $this->fileReaderRegistry->getSupportingReader($realFilename ?? $filepath)->read($filepath);

        $this->import($data);
    }

    public function import(array $data): void
    {
        $data = $this->schemaValidator->validate($data);
        dump($data);exit;
    }
}
