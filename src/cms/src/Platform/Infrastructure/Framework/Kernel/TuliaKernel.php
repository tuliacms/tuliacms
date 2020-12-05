<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Kernel;

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
    public function configureContainer(ContainerBuilderInterface $builder): void
    {
        include __DIR__ . '/../Resources/config/services-framework.php';
        include __DIR__ . '/../Resources/config/services-cms.php';

        $builder->prependExtension(new ParametersExtension());
        $builder->prependExtension(new ActivityExtension());
    }
}
