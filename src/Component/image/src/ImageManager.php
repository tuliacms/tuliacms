<?php

declare(strict_types=1);

namespace Tulia\Component\Image;

/**
 * @author Adam Banaszkiewicz
 */
class ImageManager implements ImageManagerInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param DriverInterface|null $driver
     */
    public function __construct(DriverInterface $driver = null)
    {
        $this->driver = $driver ?? DriverFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function make($input): ImageInterface
    {
        return $this->driver->make($input);
    }
}
