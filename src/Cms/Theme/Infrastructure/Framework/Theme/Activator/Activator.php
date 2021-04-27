<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    private StorageInterface $storage;
    private Options $options;
    private string $configFilename;

    public function __construct(StorageInterface $storage, Options $options, string $configFilename)
    {
        $this->storage = $storage;
        $this->options = $options;
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

        $themesDynamicConfiguration = [$name];

        $theme = $this->storage->get($name);

        if ($theme->getParent() && $this->storage->has($theme->getParent())) {
            $parentTheme = $this->storage->get($theme->getParent());

            array_unshift($themesDynamicConfiguration, $parentTheme->getName());
        }

        if (is_file($this->configFilename) && is_writable($this->configFilename) === false) {
            throw new \RuntimeException('Themes dynamic configuration file is not writabe. Cannot change active theme.');
        }

        file_put_contents(
            $this->configFilename,
            sprintf('<?php return %s;', var_export($themesDynamicConfiguration, true))
        );

        $this->options->set('theme', $name);
    }
}
