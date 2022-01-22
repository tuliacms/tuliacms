<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Service;

/**
 * @author Adam Banaszkiewicz
 */
interface TermPathReadStorageInterface
{
    public function findTermToPathGeneration(string $termId, string $locale): array;

    public function findPathByTermId(string $termId, string $locale): array;

    public function findTermIdByPath(string $path, string $locale): ?string;
}
