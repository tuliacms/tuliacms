<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Cms\Security\Framework\Security\Http\HttpUtilsUrlMatcher;

/**
 * @author Adam Banaszkiewicz
 */
class SecurityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition('security.http_utils');
        $definition->replaceArgument(1, new Reference(HttpUtilsUrlMatcher::class));
    }
}
