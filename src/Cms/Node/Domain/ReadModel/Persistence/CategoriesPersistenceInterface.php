<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Persistence;

/**
 * @author Adam Banaszkiewicz
 */
interface CategoriesPersistenceInterface
{
    public function update(string $nodeId, string $taxonomy, array $categories): void;
}
