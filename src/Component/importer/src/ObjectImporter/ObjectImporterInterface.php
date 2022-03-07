<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectImporterInterface
{
    /**
     * @return ObjectData[]|null
     */
    public function import(ObjectData $objectData): ?array;
}
