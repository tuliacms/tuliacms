<?php

declare(strict_types=1);

namespace Tulia\Component\Security\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\CommandBus\Locator\LocatorInterface;
use Tulia\Component\Security\Http\HttpUtilsUrlMatcher;
use Tulia\Component\Templating\ViewFilter\FilterInterface;

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
