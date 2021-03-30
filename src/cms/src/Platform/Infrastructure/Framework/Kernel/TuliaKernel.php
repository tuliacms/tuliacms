<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Tulia\Cms\Dashboard\Infrastructure\Framework\DependencyInjection\ActivityExtension;
use Tulia\Cms\Platform\Infrastructure\Framework\DependencyInjection\ParametersExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Kernel\Kernel;

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
        $base = __DIR__ . '/../Resources/config/config%s%s';

        $loader->load(sprintf($base, '', Kernel::CONFIG_EXTENSIONS), 'glob');
        $loader->load(sprintf($base, '-' . $env, Kernel::CONFIG_EXTENSIONS), 'glob');

        //include __DIR__ . '/../Resources/config/services-framework.php';
        //include __DIR__ . '/../Resources/config/services-cms.php';

        /*$builder->prependExtension(new ParametersExtension());
        $builder->prependExtension(new ActivityExtension());*/
    }
}
