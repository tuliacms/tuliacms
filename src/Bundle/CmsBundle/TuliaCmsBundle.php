<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tulia\Bundle\CmsBundle\DependencyInjection\TuliaCmsExtension;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new TuliaCmsExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
