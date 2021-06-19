<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ImageSize;

use Tulia\Cms\Filemanager\Ports\Domain\ImageSize\ImagesSizeProviderInterface;

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
            'thumbnail' => [
                'width'  => 200,
                'mode' => 'widen',
            ],
            'thumbnail-md' => [
                'width'  => 400,
                'mode' => 'widen',
            ],
        ];
    }
}
