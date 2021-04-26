<?php

declare(strict_types=1);

namespace Tulia\Component\Image;

/**
 * @author Adam Banaszkiewicz
 */
interface DriverInterface
{
    /**
     * @param $input
     *
     * @return ImageInterface
     */
    public function make($input): ImageInterface;
}
