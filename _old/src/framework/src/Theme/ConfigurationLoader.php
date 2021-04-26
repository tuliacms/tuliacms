<?php

declare(strict_types=1);

namespace Tulia\Framework\Theme;

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Theme\ThemeInterface;

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
    public static function load(ContainerBuilderInterface $builder, string $root, ThemeInterface $theme): void
    {
        $name = $theme->getName();

        $customizerClass = str_replace('/', '\\', "Tulia\Theme\\$name\Customizer");

        if (class_exists($customizerClass, true)) {
            $builder->setDefinition($customizerClass, $customizerClass, [
                'tags' => [ tag('theme.customizer.provider') ],
            ]);
        }

        if (is_dir($root . "/extension/theme/$name/Resources/translations")) {
            $builder->mergeParameter('translation.directory_list', [
                $root . "/extension/theme/$name/Resources/translations",
            ]);
        }

        $builder->mergeParameter('templating.paths', [
            "_theme_views/{$name}" => $root . "/extension/theme/$name/Resources/views",
        ]);

        $filename = $root . "/extension/theme/$name/Resources/config/services.php";
        is_file($filename) ? include($filename) : null;

        $filename = $root . "/extension/theme/$name/Resources/config/assets.php";
        is_file($filename) ? $builder->mergeParameter('assets', include $filename) : null;
    }
}
