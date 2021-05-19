<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
interface TermStorageInterface
{
    public function find(string $termId, string $locale): ?array;
}
