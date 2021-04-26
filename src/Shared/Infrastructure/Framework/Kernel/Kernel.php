<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Framework\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @author Adam Banaszkiewicz
 */
abstract class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__) . '/Resources/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getConfigDirs(): array
    {
        return [\dirname(__DIR__) . '/Resources/config'];
    }

    public function getPublicDir(): string
    {
        return $this->getProjectDir() . '/public';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        foreach ($this->getConfigDirs() as $root) {
            if (is_file($root . '/config.yaml')) {
                $container->import($root . '/config.yaml');
            }

            $container->import($root . '/{packages}/*.yaml');
            $container->import($root . '/{packages}/' . $this->environment . '/*.yaml');

            if (is_file($root . '/services.yaml')) {
                $container->import($root . '/services.yaml');
                $container->import($root . '/{services}_' . $this->environment . '.yaml');
            } elseif (is_file($path = $root . '/services.php')) {
                (require $path)($container->withPath($path), $this);
            }
        }

        $container->parameters()->set('kernel.public_dir', $this->getPublicDir());
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        foreach ($this->getConfigDirs() as $root) {
            if (is_file($root . '/routes.yaml')) {
                $routes->import($root . '/routes.yaml');
            }

            $routes->import($root . '/{routes}/' . $this->environment . '/*.yaml');
            $routes->import($root . '/{routes}/*.yaml');

            if (is_file($root . '/routes.yaml')) {
                $routes->import($root . '/routes.yaml');
            } elseif (is_file($path = $root . '/routes.php')) {
                (require $path)($routes->withPath($path), $this);
            }
        }
    }
}
