<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Loader;

use Tulia\Cms\Options\OptionsInterface;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeLoader implements ThemeLoaderInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var OptionsInterface
     */
    protected $options;

    /**
     * @param StorageInterface $storage
     * @param OptionsInterface $options
     */
    public function __construct(StorageInterface $storage, OptionsInterface $options)
    {
        $this->storage = $storage;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function load(): ThemeInterface
    {
        $themeName = $this->options->get('theme', '');

        if ($themeName && $this->storage->has($themeName)) {
            return $this->storage->get($themeName);
        }

        return new DefaultTheme();
    }
}
