<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
interface ProviderInterface
{
    public function provide(): array;
}
