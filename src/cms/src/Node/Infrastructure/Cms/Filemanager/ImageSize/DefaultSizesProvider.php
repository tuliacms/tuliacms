<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Filemanager\ImageSize;

use Tulia\Cms\Filemanager\Application\ImageSize\ProviderInterface;

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
            'node-thumbnail' => [
                'width'  => 450,
                'height' => 300,
            ],
        ];
    }
}
