<?php

declare(strict_types=1);

namespace Tulia\Framework\Module;

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ConfigurationLoader
{
    /**
     * @param ContainerBuilderInterface $builder
     * @param string $root
     * @param string $name
     */
    public static function load(ContainerBuilderInterface $builder, AbstractModule $module): void
    {
        $name = $module->getName();
        $root = $module->getResourcesDirectory();

        if (is_dir("{$root}/views")) {
            $builder->mergeParameter('templating.paths', [ 'module/' . strtolower($name) => "{$root}/views" ]);
        }

        if (is_dir("{$root}/translations")) {
            $builder->mergeParameter('translation.directory_list', [ "{$root}/translations" ]);
        }

        if (is_dir("{$root}/config")) {
            $builder->mergeParameter('routing.directory_list', [ "{$root}/config" ]);
        }

        $filename = "{$root}/config/services.php";
        is_file($filename) ? include($filename) : null;

        $filename = "{$root}/config/assets.php";
        is_file($filename) ? $builder->mergeParameter('assets', include $filename) : null;
    }
}
