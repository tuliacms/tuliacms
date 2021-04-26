<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Psr\Container\ContainerInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeBuilderFactory implements ThemeBuilderFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $builder;

    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @param ContainerInterface $container
     * @param string $builder
     */
    public function __construct(
        ContainerInterface $container,
        string $builder
    ) {
        $this->container = $container;
        $this->builder   = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ThemeInterface $theme): BuilderInterface
    {
        if (isset(static::$cache[$theme->getName()])) {
            return static::$cache[$theme->getName()];
        }

        /** @var BuilderInterface $builder */
        $builder = $this->container->get($this->builder);

        return static::$cache[$theme->getName()] = $builder;
    }
}
