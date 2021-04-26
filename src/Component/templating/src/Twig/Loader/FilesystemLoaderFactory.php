<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

/**
 * @author Adam Banaszkiewicz
 */
class FilesystemLoaderFactory
{
    /**
     * @param array $paths
     * @return FilesystemLoader
     * @throws LoaderError
     */
    public static function factory(array $paths): FilesystemLoader
    {
        $loader = new FilesystemLoader();

        foreach ($paths as $namespace => $path) {
            if (\is_int($namespace)) {
                $loader->addPath($path);
            } else {
                $loader->addPath($path, $namespace);
            }
        }

        return $loader;
    }
}
