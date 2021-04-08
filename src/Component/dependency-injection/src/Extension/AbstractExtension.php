<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection\Extension;

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\DependencyInjection\ContainerInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerBuilderInterface $container): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function compile(ContainerBuilderInterface $container): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container): void
    {

    }
}
