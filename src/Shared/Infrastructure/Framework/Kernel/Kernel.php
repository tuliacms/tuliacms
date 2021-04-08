<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Framework\Kernel;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
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

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $root = \dirname(__DIR__);

        $container->import($root . '/Resources/config/{packages}/*.yaml');
        $container->import($root . '/Resources/config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($root . '/Resources/config/services.yaml')) {
            $container->import($root . '/Resources/config/services.yaml');
            $container->import($root . '/Resources/config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = $root . '/Resources/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $root = \dirname(__DIR__);

        $routes->import($root . '/Resources/config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import($root . '/Resources/config/{routes}/*.yaml');

        if (is_file($root . '/Resources/config/routes.yaml')) {
            $routes->import($root . '/Resources/config/routes.yaml');
        } elseif (is_file($path = $root . '/Resources/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
