<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Persistence;

/**
 * @author Adam Banaszkiewicz
 */
interface FlagsPersistenceInterface
{
    public function update(string $nodeId, array $flags): void;
}
