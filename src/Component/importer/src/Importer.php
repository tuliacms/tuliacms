<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

use Tulia\Component\Importer\FileReader\FileReaderRegistryInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterRegistry;
use Tulia\Component\Importer\Structure\ObjectData;
use Tulia\Component\Importer\Validation\SchemaValidatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Importer implements ImporterInterface
{
    private FileReaderRegistryInterface $fileReaderRegistry;
    private SchemaValidatorInterface $schemaValidator;
    private ObjectImporterRegistry $importerRegistry;

    public function __construct(
        FileReaderRegistryInterface $fileReaderRegistry,
        SchemaValidatorInterface $schemaValidator,
        ObjectImporterRegistry $importerRegistry
    ) {
        $this->fileReaderRegistry = $fileReaderRegistry;
        $this->schemaValidator = $schemaValidator;
        $this->importerRegistry = $importerRegistry;
    }

    public function importFromFile(string $filepath, ?string $realFilename = null): void
    {
        $data = $this->fileReaderRegistry->getSupportingReader($realFilename ?? $filepath)->read($filepath);

        $this->import($data);
    }

    public function import(array $data): void
    {
        $data = $this->schemaValidator->validate($data);

        $this->importObjectCollection($data['objects']);
    }

    /**
     * @param ObjectData[] $objects
     */
    private function importObjectCollection(array $objects): void
    {
        foreach ($objects as $object) {
            if ($object->getDefinition()->getImporter() === null) {
                continue;
            }

            $importer = $this->importerRegistry->getImporter($object->getDefinition()->getImporter());

            $subobjects = $importer->import($object);

            if (is_array($subobjects) && $subobjects !== []) {
                $this->importObjectCollection($subobjects);
            }
        }
    }
}
