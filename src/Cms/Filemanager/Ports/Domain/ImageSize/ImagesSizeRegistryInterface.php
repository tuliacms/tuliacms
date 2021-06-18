<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Ports\Domain\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
interface ImagesSizeRegistryInterface
{
    public function all(): array;

    public function has(string $name): bool;

    public function get(string $name): array;
}
