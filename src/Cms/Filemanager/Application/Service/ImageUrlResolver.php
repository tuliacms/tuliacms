<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeRegistryInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlResolver
{
    private RouterInterface $router;
    private ImagesSizeRegistryInterface $imageSize;

    public function __construct(RouterInterface $router, ImagesSizeRegistryInterface $imageSize)
    {
        $this->router = $router;
        $this->imageSize = $imageSize;
    }

    public function size(File $image, string $sizeName): string
    {
        $size = $this->imageSize->get($sizeName);

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }

    public function thumbnail(File $image): string
    {
        $size = $this->imageSize->get('thumbnail');

        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $size->getCode(),
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }
}
