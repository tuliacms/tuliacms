<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Service;

use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlResolver
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function size(File $image, string $sizeName): string
    {
        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $sizeName,
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }

    public function thumbnail(File $image): string
    {
        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => 'thumbnail',
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }
}
