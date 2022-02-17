<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
interface ImagesSizeProviderInterface
{
    public function provide(): array;
}
