<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\ObjectImporter;

use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
interface ObjectImporterInterface
{
    public function import(ObjectData $objectData): ?array;
}
