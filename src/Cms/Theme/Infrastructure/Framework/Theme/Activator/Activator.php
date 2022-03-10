<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    private StorageInterface $storage;
    private OptionsRepositoryInterface $repository;
    private CurrentWebsiteInterface $currentWebsite;
    private string $configFilename;

    public function __construct(
        StorageInterface $storage,
        OptionsRepositoryInterface $repository,
        CurrentWebsiteInterface $currentWebsite,
        string $configFilename
    ) {
        $this->storage = $storage;
        $this->repository = $repository;
        $this->currentWebsite = $currentWebsite;
        $this->configFilename = $configFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function activate(string $name): void
    {
        if ($this->storage->has($name) === false) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        if (is_file($this->configFilename) && is_writable($this->configFilename) === false) {
            throw new \RuntimeException('Themes dynamic configuration file is not writable. Cannot change active theme.');
        }

        $themesDynamicConfiguration = include $this->configFilename;
        $themesDynamicConfiguration[$this->currentWebsite->getId()] = $name;

        file_put_contents(
            $this->configFilename,
            sprintf('<?php return %s;', var_export($themesDynamicConfiguration, true))
        );

        $option = $this->repository->find('theme');
        $option->setValue($name);
        $this->repository->update($option);
    }
}
