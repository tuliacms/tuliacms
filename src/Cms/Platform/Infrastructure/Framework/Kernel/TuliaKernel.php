<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Tulia\Cms\Shared\Infrastructure\Framework\Kernel\Kernel;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaKernel extends Kernel
{
    public function getPublicDir(): string
    {
        return $this->getProjectDir() . '/public';
    }

    public function getConfigDirs(): array
    {
        $base = \dirname(__DIR__, 4);

        return array_merge(
            [
                $base . '/Platform/Infrastructure/Framework/Resources/config',
                $base . '/Attributes/Infrastructure/Framework/Resources/config',
                $base . '/Activity/Framework/Resources/config',
                $base . '/BackendMenu/Infrastructure/Framework/Resources/config',
                $base . '/BodyClass/Framework/Resources/config',
                $base . '/Breadcrumbs/Framework/Resources/config',
                $base . '/ContactForm/Infrastructure/Framework/Resources/config',
                $base . '/EditLinks/Framework/Resources/config',
                $base . '/Filemanager/Infrastructure/Framework/Resources/config',
                $base . '/FrontendToolbar/Framework/Resources/config',
                $base . '/Homepage/Infrastructure/Framework/Resources/config',
                //$base . '/Installator/Infrastructure/Framework/Resources/config',
                $base . '/Menu/Infrastructure/Framework/Resources/config',
                $base . '/Node/Infrastructure/Framework/Resources/config',
                $base . '/Options/Infrastructure/Framework/Resources/config',
                $base . '/SearchAnything/Framework/Resources/config',
                $base . '/Security/Framework/Resources/config',
                $base . '/Settings/Infrastructure/Framework/Resources/config',
                $base . '/Taxonomy/Infrastructure/Framework/Resources/config',
                $base . '/Theme/Infrastructure/Framework/Resources/config',
                $base . '/User/Infrastructure/Framework/Resources/config',
                $base . '/Website/Infrastructure/Framework/Resources/config',
                $base . '/Widget/Infrastructure/Framework/Resources/config',
                $base . '/WysiwygEditor/Infrastructure/Framework/Resources/config',
                $base . '/TuliaEditor/Infrastructure/Framework/Resources/config',
                $base . '/ContentBuilder/Infrastructure/Framework/Resources/config',
                $base . '/ContentBlock/Infrastructure/Framework/Resources/config',
            ],
            $this->getThemesConfigDirs(),
            $this->getModulesConfigDirs()
        );
    }

    public function getThemesConfigDirs(): array
    {
        $configDirs = [];

        foreach (new \DirectoryIterator($this->getProjectDir().'/extension/theme') as $vendor) {
            if ($vendor->isDot()) {
                continue;
            }

            foreach (new \DirectoryIterator($this->getProjectDir().'/extension/theme/'.$vendor->getFilename()) as $theme) {
                if ($vendor->isDot()) {
                    continue;
                }

                $path = $this->getProjectDir().'/extension/theme/'.$vendor->getFilename().'/'.$theme->getFilename().'/Resources/config';

                if (is_dir($path)) {
                    $configDirs[] = $path;
                }
            }
        }

        return $configDirs;
    }

    public function getModulesConfigDirs(): array
    {
        $configDirs = [];

        foreach (new \DirectoryIterator($this->getProjectDir().'/extension/module') as $vendor) {
            if ($vendor->isDot()) {
                continue;
            }

            foreach (new \DirectoryIterator($this->getProjectDir().'/extension/module/'.$vendor->getFilename()) as $module) {
                if ($vendor->isDot()) {
                    continue;
                }

                $path = $this->getProjectDir().'/extension/module/'.$vendor->getFilename().'/'.$module->getFilename().'/Resources/config';

                if (is_dir($path)) {
                    $configDirs[] = $path;
                }
            }
        }

        return $configDirs;
    }

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__) . '/Resources/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }
}
