<?php

declare(strict_types=1);

namespace Tulia\Component\Image;

use Intervention\Image\ImageManager as Intervention;
use Tulia\Component\Image\Exception\DriverNotFound;
use Tulia\Component\Image\Implementation\Intervention\Driver as InterventionDriver;

/**
 * @author Adam Banaszkiewicz
 */
class DriverFactory
{
    public static function create()
    {
        if (class_exists(Intervention::class)) {
            return new ImageManager(new InterventionDriver(new Intervention([
                'driver' => extension_loaded('imagick') ? 'imagick' : 'gd',
            ])));
        }

        $suggests = 'intervention/image';

        throw new DriverNotFound(sprintf('None of drivers are installed. Please install one of those: %s, using composer.', $suggests));
    }
}
