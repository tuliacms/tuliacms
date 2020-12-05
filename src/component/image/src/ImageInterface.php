<?php

declare(strict_types=1);

namespace Tulia\Component\Image;

/**
 * @author Adam Banaszkiewicz
 */
interface ImageInterface
{
    public function fit(int $width, int $height, callable $callback = null, string $position = 'center'): ImageInterface;
    public function widen(int $width, \Closure $callback = null): ImageInterface;
    public function heighten(int $height, \Closure $callback = null): ImageInterface;
    public function resize(int $width = null, int $height = null, \Closure $callback = null): ImageInterface;
    public function save($path = null, $quality = null, $format = null): ImageInterface;
}
