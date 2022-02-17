<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Filemanager\ImageSize;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultSizesImagesSizeProvider implements ImagesSizeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provide(): array
    {
        return [
            'node-thumbnail' => [
                'width'  => 450,
                'height' => 300,
            ],
        ];
    }
}
