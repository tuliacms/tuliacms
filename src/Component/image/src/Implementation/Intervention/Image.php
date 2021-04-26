<?php

declare(strict_types=1);

namespace Tulia\Component\Image\Implementation\Intervention;

use Tulia\Component\Image\ImageInterface;
use Intervention\Image\Image as InterventionImage;

/**
 * @author Adam Banaszkiewicz
 */
class Image implements ImageInterface
{
    /**
     * @var InterventionImage
     */
    private $image;

    /**
     * @param InterventionImage $image
     */
    public function __construct(InterventionImage $image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function fit(int $width, int $height, callable $callback = null, string $position = 'center'): ImageInterface
    {
        return $this->result($this->image->fit($width, $height, $callback, $position));
    }

    /**
     * {@inheritdoc}
     */
    public function widen(int $width, \Closure $callback = null): ImageInterface
    {
        return $this->result($this->image->widen($width, $callback));
    }

    /**
     * {@inheritdoc}
     */
    public function heighten(int $height, \Closure $callback = null): ImageInterface
    {
        return $this->result($this->image->heighten($height, $callback));
    }

    /**
     * {@inheritdoc}
     */
    public function resize(int $width = null, int $height = null, \Closure $callback = null): ImageInterface
    {
        return $this->result($this->image->resize($width, $height, $callback));
    }

    /**
     * {@inheritdoc}
     */
    public function save($path = null, $quality = null, $format = null): ImageInterface
    {
        return $this->result($this->image->save($path, $quality, $format));
    }

    protected function result($result): ImageInterface
    {
        if ($result instanceof InterventionImage) {
            return $this;
        }

        return $result;
    }
}
