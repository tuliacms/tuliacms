<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Registrator;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistratorInterface
{
    /**
     * @param RegistryInterface $registry
     *
     * @return mixed
     */
    public function register(RegistryInterface $registry): void;
}
