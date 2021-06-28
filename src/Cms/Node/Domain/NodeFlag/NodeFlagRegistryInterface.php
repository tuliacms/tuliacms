<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFlagRegistryInterface
{
    public function all(): array;
}
