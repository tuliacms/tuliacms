<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\TwigBridge\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Loader\FilesystemLoader;
use Tulia\Component\Theme\ManagerInterface;
use Twig\Source;

/**
 * @author Adam Banaszkiewicz
 */
class NamespaceLoader implements LoaderInterface
{
    /**
     * @var FilesystemLoader
     */
    protected $loader;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name): Source
    {
        $this->resolveLoader();

        return $this->loader->getSourceContext($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name): string
    {
        $this->resolveLoader();

        return $this->loader->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time): bool
    {
        $this->resolveLoader();

        return $this->loader->isFresh($name, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $this->resolveLoader();

        return $this->loader->exists($name);
    }

    /**
     * Makes this loader to be lazy.
     */
    private function resolveLoader(): void
    {
        if ($this->loader !== null) {
            return;
        }

        $this->loader = new FilesystemLoader;

        $theme = $this->manager->getTheme();

        $this->loader->addPath($theme->getViewsDirectory(), 'theme');

        if ($theme->getParent()) {
            $parent = $this->manager->getStorage()->get($theme->getParent());

            $this->loader->addPath($parent->getViewsDirectory(), 'parent');
        }
    }
}
