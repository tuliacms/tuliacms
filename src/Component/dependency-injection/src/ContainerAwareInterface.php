<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Psr\Container\ContainerInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container): void;
}
