<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Loader;

use Tulia\Component\Templating\Twig\Loader\AdvancedFilesystemLoader;
use Tulia\Component\Templating\ViewFilter\FilterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AdvancedFilesystemLoaderFactory
{
    public static function factory(FilterInterface $filter, array $paths = [], string $rootPath = null): AdvancedFilesystemLoader
    {
        $prepared = [];

        foreach ($paths as $path) {
            $prepared["@{$path['name']}"] = $path['path'];
        }

        return new AdvancedFilesystemLoader($filter, $prepared);
    }
}
