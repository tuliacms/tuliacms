<?php

declare(strict_types=1);

namespace Tulia\Component\Image\Implementation\Intervention;

use Intervention\Image\ImageManager as Intervention;
use Tulia\Component\Image\DriverInterface;
use Tulia\Component\Image\ImageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Driver implements DriverInterface
{
    /**
     * @var Intervention
     */
    protected $imageManager;

    /**
     * @param Intervention $imageManager
     */
    public function __construct(Intervention $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function make($input): ImageInterface
    {
        return new Image($this->imageManager->make($input));
    }
}
