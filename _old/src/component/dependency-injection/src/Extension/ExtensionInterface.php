<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection\Extension;

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\DependencyInjection\ContainerInterface;
use Tulia\Component\DependencyInjection\Exception\MissingParameterException;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionInterface
{
    /**
     * @param ContainerBuilderInterface $container
     *
     * @throws MissingParameterException
     */
    public function register(ContainerBuilderInterface $container): void;

    /**
     * @param ContainerBuilderInterface $container
     *
     * @throws MissingParameterException
     */
    public function compile(ContainerBuilderInterface $container): void;

    /**
     * @param ContainerInterface $container
     *
     * @throws MissingParameterException
     */
    public function build(ContainerInterface $container): void;
}
