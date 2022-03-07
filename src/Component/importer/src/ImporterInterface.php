<?php

declare(strict_types=1);

namespace Tulia\Component\Importer;

/**
 * @author Adam Banaszkiewicz
 */
interface ImporterInterface
{
    /**
     * @throws Exception\FileNotSupportedException
     */
    public function importFromFile(string $filepath): void;

    public function import(array $data): void;
}
