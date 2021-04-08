<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\CommandBusPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\TemplatingPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\TuliaCmsExtension;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaCmsBundle extends FrameworkBundle
{
    protected $name = 'FrameworkBundle';

    public function getContainerExtension(): ExtensionInterface
    {
        return new TuliaCmsExtension();
    }

    public function getNamespace(): string
    {
        return 'Symfony\Bundle\FrameworkBundle';
    }

    public function getPath(): string
    {
        return \dirname(__DIR__, 3) . '/vendor/symfony/framework-bundle';
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new CommandBusPass());
    }
}
