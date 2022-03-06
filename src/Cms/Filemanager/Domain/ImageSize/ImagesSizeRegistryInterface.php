<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
interface ImagesSizeRegistryInterface
{
    /** @return ImageSize[] */
    public function all(): array;
    public function has(string $name): bool;
    public function get(string $name): ImageSize;
}
