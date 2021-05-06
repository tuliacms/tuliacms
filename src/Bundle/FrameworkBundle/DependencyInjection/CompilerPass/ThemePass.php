<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Adam Banaszkiewicz
 */
class ThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {

    }
}
