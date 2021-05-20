<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
interface TermPathReadStorageInterface
{
    public function find(string $termId, string $locale): array;

    public function findTermIdByPath(string $path, string $locale): ?string;
}
