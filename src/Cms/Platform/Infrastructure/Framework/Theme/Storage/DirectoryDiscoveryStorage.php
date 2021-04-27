<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Theme\Storage;

use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DirectoryDiscoveryStorage implements StorageInterface
{
    private array $themes = [];
    private string $extensionsDirectory;

    public function __construct(string $extensionsDirectory)
    {
        $this->extensionsDirectory = $extensionsDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        $this->resolveThemes();

        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ThemeInterface $theme): void
    {
        $this->resolveThemes();

        $this->themes[$theme->getName()] = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): ThemeInterface
    {
        $this->resolveThemes();

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
        $this->resolveThemes();

        return isset($this->themes[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->resolveThemes();

        return new \ArrayIterator($this->themes);
    }

    private function resolveThemes(): void
    {
        foreach (new \DirectoryIterator($this->extensionsDirectory) as $namespace) {
            if ($namespace->isDot()) {
                continue;
            }

            foreach (new \DirectoryIterator($this->extensionsDirectory . '/' . $namespace->getFilename()) as $theme) {
                if ($theme->isDot()) {
                    continue;
                }

                $themeClassname = 'Tulia\\Theme\\' . $namespace->getFilename() . '\\' .  $theme->getFilename() . '\\Theme';
                /** @var ThemeInterface $themeObject */
                $themeObject = new $themeClassname();

                $this->themes[$themeObject->getName()] = $themeObject;
            }
        }
    }
}
