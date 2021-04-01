<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tulia\Cms\Dashboard\Infrastructure\Framework\DependencyInjection\ActivityExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\DependencyInjection\ParametersExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\Package\PlatformPackage;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Kernel\Kernel;
use Tulia\Framework\Package\FrameworkPackage;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $env = $c->getParameter('kernel.environment');

        foreach ($this->getConfigDirs() as $dir) {
            $base = $dir . '/config%s%s';

            $loader->load(sprintf($base, '', Kernel::CONFIG_EXTENSIONS), 'glob');
            $loader->load(sprintf($base, '-' . $env, Kernel::CONFIG_EXTENSIONS), 'glob');

           //$loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
           //$loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
           //$loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
           //$loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
           //$loader->load($confDir.'/{services}/**/*'.self::CONFIG_EXTS, 'glob'); // <-- this
        }

        /*$builder->prependExtension(new ParametersExtension());
        $builder->prependExtension(new ActivityExtension());*/
    }

    public function registerPackages(): array
    {
        return [
            new FrameworkPackage(),
            new PlatformPackage(),
        ];
    }

    public static function getConfigDirs(): array
    {
        $base = \dirname(__DIR__, 4);

        return [
            $base . '/Platform/Infrastructure/Framework/Resources/config',
            $base . '/BodyClass/Infrastructure/Framework/Resources/config',
            $base . '/Options/Infrastructure/Framework/Resources/config',
            $base . '/Homepage/Infrastructure/Framework/Resources/config',
            $base . '/Theme/Infrastructure/Framework/Resources/config',
            $base . '/Website/Infrastructure/Framework/Resources/config',
            $base . '/Breadcrumbs/Infrastructure/Framework/Resources/config',
            $base . '/Menu/Infrastructure/Framework/Resources/config',
        ];
    }
}
