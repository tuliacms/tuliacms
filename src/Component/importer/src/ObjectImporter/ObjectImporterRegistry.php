<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

/**
 * @author Adam Banaszkiewicz
 */
class ObjectImporterRegistry
{
    /** @var ObjectImporterInterface[]  */
    private array $importers = [];

    public function addObjectImporter(ObjectImporterInterface $importer): void
    {
        $this->importers = $importer;
    }
}
