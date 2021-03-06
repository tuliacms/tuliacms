<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistratorInterface
{
    /**
     * @param RegistryInterface $registry
     */
    public function register(RegistryInterface $registry): void;
}
