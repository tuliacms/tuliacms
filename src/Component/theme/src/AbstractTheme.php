<?php

declare(strict_types=1);

namespace Tulia\Component\Theme;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractTheme implements ThemeInterface
{
    protected $name;
    protected $vendor;
    protected $directory;
    protected $parent;
    protected $config;
    protected $version = '0.0.1';
    protected $author;
    protected $authorUrl;
    protected $info;
    protected $thumbnail;

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $this->resolveName();

        return $this->name;
    }

    public function getVendor(): string
    {
        if ($this->vendor) {
            return $this->vendor;
        }

        $this->resolveName();

        return $this->vendor;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDirectory(): string
    {
        if (! $this->directory) {
            $this->directory = $this->resolveDirectory();
        }

        return $this->directory;
    }

    public function getViewsDirectory(): string
    {
        return $this->getDirectory().'/Resources/views';
    }

    protected function resolveDirectory(): string
    {
        return realpath(\dirname((new \ReflectionClass($this))->getFileName()));
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig(): bool
    {
        return (bool) $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigurationInterface
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(ConfigurationInterface $config): void
    {
        $this->config = $config;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    public function getInfo()
    {
        return $this->info;
    }

    protected function resolveName(): void
    {
        [, , $this->vendor, $name] = explode('\\', \get_class($this));
        $this->name = $this->vendor . '/' . $name;

        if (empty($this->name)) {
            throw new \RuntimeException('Cannot resolve $name of the theme. Please provide $name property for Your theme.');
        }
    }
}
