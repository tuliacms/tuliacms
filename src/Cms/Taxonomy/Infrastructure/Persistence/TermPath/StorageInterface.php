<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function find(string $termId, string $locale): array;
    public function remove(string $termId, string $locale): void;
    public function save(string $termId, string $locale, string $path): void;
    public function findByPath(string $path, string $locale): ?string;
}
