<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\TaxonomyType\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(): array;
}
