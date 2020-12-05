<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Framework\Module\AbstractModule;
use Tulia\Framework\Theme\ConfigurationLoader as ThemeConfigurationLoader;
use Tulia\Framework\Module\ConfigurationLoader as ModuleConfigurationLoader;
use Tulia\Framework\Http\Request;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Adam Banaszkiewicz
 */
abstract class Kernel implements KernelInterface
{
    protected $environment;
    protected $debug;
    protected $projectDir;

    protected $themes = [];
    protected $modules = [];

    protected $startTime = 0.0;
    protected $booted    = false;

    /**
     * @var PsrContainerInterface
     */
    protected $container;

    /**
     * @param string $environment
     * @param bool $debug
     */
    public function __construct(string $environment, bool $debug)
    {
        $this->environment = $environment;
        $this->debug       = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer(): PsrContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): Response
    {
        $this->boot();

        $kernel = $this->getHttpKernel();
        $kernel->bootstrap($request);
        return $kernel->handle($request);
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        if (! $this->booted) {
            return;
        }

        $this->getHttpKernel()->terminate($request, $response);
    }

    /**
     * @return HttpKernelInterface
     */
    public function getHttpKernel(): HttpKernelInterface
    {
        return $this->container->get(HttpKernelInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionsDir(): string
    {
        return $this->getProjectDir().'/extension';
    }

    /**
     * {@inheritdoc}
     */
    public function configureContainer(ContainerBuilderInterface $builder): void
    {

    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->startTime = microtime(true);

        $builder = new ContainerBuilder();

        foreach ($this->getKernelParameters() as $id => $value) {
            $builder->setParameter($id, $value);
        }

        include __DIR__ . '/../Resources/config/services.php';

        $this->configureContainer($builder);
        $this->configureContainerForModules($builder);
        $this->configureContainerForThemes($builder);

        $this->container = $builder->compile();
        $this->container->set(KernelInterface::class, $this);
        $this->container->lock();

        $this->booted = true;
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

    /**
     * @return array
     */
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
