<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\Resolver\ResolverAggregateInterface;
use Tulia\Component\Theme\Loader\ThemeLoader\ThemeLoaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    protected $theme;
    protected $storage;
    protected $resolver;
    protected $loader;

    /**
     * @param StorageInterface           $storage
     * @param ResolverAggregateInterface $resolver
     * @param ThemeLoaderInterface       $loader
     */
    public function __construct(StorageInterface $storage, ResolverAggregateInterface $resolver, ThemeLoaderInterface $loader)
    {
        $this->storage  = $storage;
        $this->resolver = $resolver;
        $this->loader   = $loader;
    }

    public function getTheme(): ThemeInterface
    {
        if ($this->theme) {
            return $this->theme;
        }

        $this->theme = $this->loader->load();

        $this->resolver->resolve($this->theme);

        return $this->theme;
    }

    public function setTheme(ThemeInterface $theme): void
    {
        $this->theme = $theme;

        $this->resolver->resolve($this->theme);
    }

    public function getThemes(): iterable
    {
        return $this->storage->all();
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    public function getResolver(): ResolverAggregateInterface
    {
        return $this->resolver;
    }

    public function setResolver(ResolverAggregateInterface $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function getLoader(): ThemeLoaderInterface
    {
        return $this->loader;
    }

    public function setLoader(ThemeLoaderInterface $loader): void
    {
        $this->loader = $loader;
    }
}
