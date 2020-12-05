<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Storage;

use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    /**
     * @var iterable
     */
    protected $themes = [];

    /**
     * @param iterable $themes
     */
    public function __construct(iterable $themes = [])
    {
        foreach($themes as $theme) {
            $this->add($theme);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ThemeInterface $theme): void
    {
        $this->themes[$theme->getName()] = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): ThemeInterface
    {
        if (!isset($this->themes[$name])) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        return $this->themes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->themes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->themes);
    }
}
