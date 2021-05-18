<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(): array;
}
