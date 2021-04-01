<?php

declare(strict_types=1);

namespace Tulia\Framework\Package;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface as ContainerExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractPackage implements PackageInterface
{
    public function build(ContainerBuilder $builder): void
    {
    }

    public function getContainerExtension(): ?ContainerExtensionInterface
    {
        return null;
    }
}
