<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeTypeRegistratorInterface
{
    public function register(NodeTypeRegistryInterface $registry): void;
}
