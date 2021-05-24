<?php

declare(strict_types=1);

namespace Tulia\Bundle\FrameworkBundle;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass\CommandBusPass;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass\FinderPass;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass\RoutingPass;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass\TemplatingPass;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\CompilerPass\ThemePass;
use Tulia\Bundle\FrameworkBundle\DependencyInjection\TuliaCmsExtension;
use Tulia\Component\Security\DependencyInjection\CompilerPass\SecurityPass;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaFrameworkBundle extends FrameworkBundle
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

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->ensureDynamicConfigFileExists($container, '/config/dynamic/themes.php');
        $this->ensureDynamicConfigFileExists($container, '/config/dynamic/modules.php');

        $container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new CommandBusPass());
        $container->addCompilerPass(new RoutingPass());
        $container->addCompilerPass(new ThemePass());
        $container->addCompilerPass(new SecurityPass());
        $container->addCompilerPass(new FinderPass());
    }

    private function ensureDynamicConfigFileExists(ContainerBuilder $container, string $path): void
    {
        $filepath = $container->getParameter('kernel.project_dir') . $path;

        if (is_file($filepath) === false) {
            file_put_contents($filepath, '<?php return [];');
        }

        $container->addResource(new FileResource($filepath));
    }
}
