<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

use Tulia\Cms\Node\Domain\NodeFlag\Exception\FlagNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFlagRegistryInterface
{
    public function all(): array;

    /**
     * @throws FlagNotFoundException
     */
    public function isSingular(string $name): bool;
}
