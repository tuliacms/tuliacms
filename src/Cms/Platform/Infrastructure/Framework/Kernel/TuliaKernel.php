<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Tulia\Cms\Shared\Infrastructure\Framework\Kernel\Kernel;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaKernel extends Kernel
{
    public function getConfigDirs(): array
    {
        $base = \dirname(__DIR__, 4);

        return array_merge(
            parent::getConfigDirs(),
            [
                $base . '/Activity/Infrastructure/Framework/Resources/config',
                $base . '/BackendMenu/Infrastructure/Framework/Resources/config',
                $base . '/BodyClass/Infrastructure/Framework/Resources/config',
                $base . '/Breadcrumbs/Infrastructure/Framework/Resources/config',
                $base . '/ContactForm/Infrastructure/Framework/Resources/config',
                $base . '/Dashboard/Infrastructure/Framework/Resources/config',
                $base . '/EditLinks/Infrastructure/Framework/Resources/config',
                $base . '/Filemanager/Infrastructure/Framework/Resources/config',
                $base . '/FrontendToolbar/Infrastructure/Framework/Resources/config',
                $base . '/Homepage/Infrastructure/Framework/Resources/config',
                //$base . '/Installator/Infrastructure/Framework/Resources/config',
                $base . '/Menu/Infrastructure/Framework/Resources/config',
                $base . '/Node/Infrastructure/Framework/Resources/config',
                $base . '/Options/Infrastructure/Framework/Resources/config',
                $base . '/Platform/Infrastructure/Framework/Resources/config',
                $base . '/SearchAnything/Infrastructure/Framework/Resources/config',
                $base . '/Security/Infrastructure/Framework/Resources/config',
                $base . '/Settings/Infrastructure/Framework/Resources/config',
                $base . '/Taxonomy/Infrastructure/Framework/Resources/config',
                $base . '/Theme/Infrastructure/Framework/Resources/config',
                $base . '/User/Infrastructure/Framework/Resources/config',
                $base . '/Website/Infrastructure/Framework/Resources/config',
                $base . '/Widget/Infrastructure/Framework/Resources/config',
                $base . '/WysiwygEditor/Core/Infrastructure/Framework/Resources/config',
                $base . '/TuliaEditor/Infrastructure/Framework/Resources/config',
            ],
            $this->getActiveThemesConfigDirs(),
            $this->getActiveModulesConfigDirs()
        );
    }

    public function getActiveThemesConfigDirs(): array
    {
        $filepath = $this->getProjectDir() . '/config/dynamic/themes.php';

        if (is_file($filepath) === false) {
            return [];
        }

        $base = \dirname(__DIR__, 6) . '/extension/theme';
        $themes = include $filepath;
        $configDirs = [];

        foreach ($themes as $theme) {
            $path = $base . '/' . $theme . '/Resources/config';

            if (is_dir($path)) {
                $configDirs[] = $path;
            }
        }

        return $configDirs;
    }

    public function getActiveModulesConfigDirs(): array
    {
        $filepath = $this->getProjectDir() . '/config/dynamic/modules.php';

        if (is_file($filepath) === false) {
            return [];
        }

        $base = \dirname(__DIR__, 6) . '/extension/module';
        $modules = include $filepath;
        $configDirs = [];

        foreach ($modules as $module) {
            $path = $base . '/' . $module . '/Resources/config';

            if (is_dir($path)) {
                $configDirs[] = $path;
            }
        }

        return $configDirs;
    }
}
