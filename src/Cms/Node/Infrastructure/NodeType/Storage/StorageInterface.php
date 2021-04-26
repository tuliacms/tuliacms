<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\NodeType\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function all(): array;
}
