<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistratorInterface
{
    public function register(RegistryInterface $registry): void;
}
