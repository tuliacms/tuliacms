<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Persistence\Domain\ImageSize;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider implements ImagesSizeProviderInterface
{
    private array $imageSizes;

    public function __construct(array $imageSizes)
    {
        $this->imageSizes = $imageSizes;
    }

    public function provide(): array
    {
        return $this->imageSizes;
    }
}
