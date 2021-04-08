<?php

declare(strict_types=1);

namespace Tulia\Component\Image;

/**
 * @author Adam Banaszkiewicz
 */
interface ImageManagerInterface
{
    /**
     * @param $input
     * @return ImageInterface
     */
    public function make($input): ImageInterface;
}
