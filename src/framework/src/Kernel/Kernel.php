<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Framework\Kernel\Config\FileLocator;
use Tulia\Framework\Module\AbstractModule;
use Tulia\Framework\Package\FrameworkPackage;
use Tulia\Framework\Package\PackageInterface;
use Tulia\Framework\Theme\ConfigurationLoader as ThemeConfigurationLoader;
use Tulia\Framework\Module\ConfigurationLoader as ModuleConfigurationLoader;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
abstract class Kernel implements KernelInterface
{
    public const CONFIG_EXTENSIONS = '.{php,xml,yaml,yml}';

    protected string $environment;
    protected string $projectDir;

    protected array $themes = [];
    protected array $modules = [];
    protected array $packages = [];

    protected float $startTime = 0.0;
    protected bool $debug;
    protected bool $booted = false;

    protected PsrContainerInterface $container;

    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug = $debug;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function getContainer(): PsrContainerInterface
    {
        return $this->container;
    }

    public function handle(Request $request): Response
    {
        $this->boot();

        $kernel = $this->getHttpKernel();
        $kernel->bootstrap($request);
        return $kernel->handle($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->booted) {
            return;
        }

        $this->getHttpKernel()->terminate($request, $response);
    }

    public function getHttpKernel(): HttpKernelInterface
    {
        return $this->container->get(HttpKernelInterface::class);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/'.$this->environment;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function getExtensionsDir(): string
    {
        return $this->getProjectDir() . '/extension';
    }

    public function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
    }

    public function registerPackages(): array
    {
        return [];
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->startTime = microtime(true);

        $this->packages = $this->registerPackages();

        $container = new ContainerBuilder();
        $container->getParameterBag()->add($this->getKernelParameters());
        $container->addObjectResource($this);
        $this->prepareContainer($container);

        $loader = $this->getContainerLoader($container);
        $this->registerContainerConfiguration($loader);

        $this->configureContainer($container, $loader);

        $container->compile();

        $this->booted = true;

        $this->container = $container;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../Resources/config/services.yaml');
    }

    private function prepareContainer(ContainerBuilder $container): void
    {
        $extensions = [];

        /** @var PackageInterface $package */
        foreach ($this->packages as $package) {
            if ($extension = $package->getContainerExtension()) {
                $container->registerExtension($extension);
            }

            if ($this->debug) {
                $container->addObjectResource($package);
            }
        }

        /** @var PackageInterface $package */
        foreach ($this->packages as $package) {
            $package->build($container);
        }

        //$this->build($container);

        foreach ($container->getExtensions() as $extension) {
            $extensions[] = $extension->getAlias();
        }

        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
    }

    protected function getContainerLoader(ContainerBuilder $container): DelegatingLoader
    {
        $locator = new FileLocator($this);
        $resolver = new LoaderResolver([
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new GlobFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ]);

        return new DelegatingLoader($resolver);
    }

    protected function configureContainerForThemes(ContainerBuilderInterface $builder): void
    {
        $vendors = array_diff(scandir($this->getExtensionsDir() . '/theme'), ['.', '..']);

        foreach ($vendors as $vendor) {
            $themes = array_diff(scandir($this->getExtensionsDir() . '/theme/' . $vendor), ['.', '..']);

            foreach ($themes as $name) {
                $themeClassname = "Tulia\\Theme\\{$vendor}\\{$name}\\Theme";

                if (class_exists($themeClassname, true) === false) {
                    continue;
                }

                $group = $builder->getGroup("theme.{$vendor}/{$name}");
                $this->themes[$name] = $theme = new $themeClassname;

                (function (ContainerBuilderInterface $builder, string $root, ThemeInterface $theme) {
                    ThemeConfigurationLoader::load($builder, $root, $theme);
                })($group, $this->getProjectDir(), $theme);
            }
        }

        $builder->setParameter('kernel.themes', $this->themes);
    }

    protected function configureContainerForModules(ContainerBuilderInterface $builder): void
    {
        $vendors = array_diff(scandir($this->getExtensionsDir() . '/module'), ['.', '..']);

        foreach ($vendors as $vendor) {
            $modules = array_diff(scandir($this->getExtensionsDir() . '/module/' . $vendor), ['.', '..']);

            foreach ($modules as $name) {
                $moduleClassname = "Tulia\\Module\\{$vendor}\\{$name}\\Module";

                if (class_exists($moduleClassname, true) === false) {
                    continue;
                }

                $group = $builder->getGroup("module.{$vendor}/{$name}");
                $this->modules[$name] = $module = new $moduleClassname;

                (function (ContainerBuilderInterface $builder, AbstractModule $module) {
                    ModuleConfigurationLoader::load($builder, $module);
                })($group, $module);
            }
        }

        $builder->setParameter('kernel.modules', $this->modules);
    }

    protected function getKernelParameters(): array
    {
        return [
            'kernel.project_dir' => realpath($this->getProjectDir()) ?: $this->getProjectDir(),
            'kernel.system_dir'  => $this->getProjectDir(),
            'kernel.config_dir'  => $this->getProjectDir() . '/config',
            'kernel.public_dir'  => $this->getProjectDir() . '/public',
            'kernel.cache_dir'   => $this->getCacheDir(),
            'kernel.logs_dir'    => realpath($this->getLogDir()) ?: $this->getLogDir(),
            'kernel.environment' => $this->environment,
            'kernel.debug'       => $this->debug,
        ];
    }
}
