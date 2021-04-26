<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application;

use Tulia\Cms\Filemanager\File;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImageUrlResolver
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param File $image
     * @param string $sizeName
     *
     * @return string
     */
    public function size(File $image, string $sizeName): string
    {
        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => $sizeName,
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }

    /**
     * @param File $image
     *
     * @return string
     */
    public function thumbnail(File $image): string
    {
        return $this->router->generate('filemanager.resolve.image.size', [
            'size' => 'thumbnail',
            'id'   => $image->getId(),
            'filename' => $image->getFilename(),
        ]);
    }
}
