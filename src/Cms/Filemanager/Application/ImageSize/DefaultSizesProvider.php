<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultSizesProvider implements ProviderInterface
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
