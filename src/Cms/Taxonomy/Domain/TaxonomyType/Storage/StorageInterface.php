<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Domain\TaxonomyType\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(): array;
}
