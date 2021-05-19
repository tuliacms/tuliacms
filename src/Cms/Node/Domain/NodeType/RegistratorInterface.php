<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

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
