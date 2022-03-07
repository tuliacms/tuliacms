<?php

declare(strict_types=1);

namespace Tulia\Bundle\CmsBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\ContentBuilderPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\DashboardPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\DomainActionChainPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\MenuPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\TaxonomyPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\CompilerPass\WidgetPass;
use Tulia\Bundle\CmsBundle\DependencyInjection\TuliaCmsExtension;
use Tulia\Component\Importer\Implementation\Symfony\DependencyInjection\CompilerPass\ImporterPass;

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
        $container->addCompilerPass(new DomainActionChainPass());
        $container->addCompilerPass(new TaxonomyPass());
        $container->addCompilerPass(new MenuPass());
        $container->addCompilerPass(new DashboardPass());
        $container->addCompilerPass(new WidgetPass());
        $container->addCompilerPass(new ContentBuilderPass());
        $container->addCompilerPass(new ImporterPass());
    }
}
