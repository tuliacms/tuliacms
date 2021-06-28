<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFlagProviderInterface
{
    public function provide(): array;
}
