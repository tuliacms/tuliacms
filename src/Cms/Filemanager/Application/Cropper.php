<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application;

use Tulia\Cms\Filemanager\Application\ImageSize\Registry;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\FileInterface;
use Tulia\Component\Image\ImageInterface;
use Tulia\Component\Image\ImageManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Cropper
{
    /**
     * @var ImageManagerInterface
     */
    protected $imageManager;

    /**
     * @var Registry
     */
    protected $imageSize;

    /**
     * @var string
     */
    protected $filesDirectory;

    /**
     * @param ImageManagerInterface $imageManager
     * @param Registry $imageSize
     * @param string $filesDirectory
     */
    public function __construct(ImageManagerInterface $imageManager, Registry $imageSize, string $filesDirectory)
    {
        $this->imageManager   = $imageManager;
        $this->imageSize      = $imageSize;
        $this->filesDirectory = $filesDirectory;
    }

    public function crop(FileInterface $image, string $sizeName): string
    {
        if ($image->getType() !== TypeEnum::IMAGE) {
            throw new \InvalidArgumentException(sprintf('First argument of crop() method must be an image, "%s" given.', $image->getType()));
        }

        if ($this->imageSize->has($sizeName) === false) {
            throw new \InvalidArgumentException(sprintf('Image size not found in registered sizes, "%s" given.', $sizeName));
        }

        $size = $this->imageSize->get($sizeName);

        $directory = substr($image->getId(), 0, 2) . '/' . substr($image->getId(), 2, 2) . '/' . substr($image->getId(), 4, 2) . '/' . $image->getId();
        $name      = pathinfo($image->getFilename(), PATHINFO_FILENAME);

        $source = $this->filesDirectory . '/' . $image->getPath() . '/' . $image->getFilename();
        $output = '/uploads/thumbnails/' . $sizeName . '/' . $directory . '/' . $name . '.' . $image->getExtension();

        if (is_dir(\dirname($this->filesDirectory . $output)) === false) {
            if (!mkdir($concurrentDirectory = \dirname($this->filesDirectory . $output), 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        $img = $this->imageManager->make($source);
        $this->cropMode($img, $size);
        $img->save($this->filesDirectory . $output);

        return $output;
    }

    private function cropMode(ImageInterface $image, array $size): void
    {
        if ($size['mode'] === 'fit') {
            $image->fit($size['width'], $size['height']);
        } elseif ($size['mode'] === 'widen') {
            $image->widen($size['width']);
        } elseif ($size['mode'] === 'heighten') {
            $image->heighten($size['width']);
        } elseif ($size['mode'] === 'resize') {
            $image->resize($size['width'], $size['height'], function ($constraint) {
                $constraint->aspectRatio();
            });
        }
    }
}
