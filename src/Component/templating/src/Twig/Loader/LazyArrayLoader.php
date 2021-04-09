<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Loader\ArrayLoader;
use Twig\Source;

/**
 * @author Adam Banaszkiewicz
 */
class LazyArrayLoader implements LoaderInterface
{
    protected ?ArrayLoader $loader = null;
    protected array $templates = [];

    public function __construct(array $templates)
    {
        $this->templates = $templates;
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
    public function exists($name): bool
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

        $this->loader = new ArrayLoader();

        foreach ($this->templates as $name => $template) {
            $this->loader->setTemplate($name, $template);
        }
    }
}
