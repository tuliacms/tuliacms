<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Ports;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeByFlagFinderInterface
{
    public function findOtherNodesWithFlags(string $localNode, array $flags, string $websiteId): array;
}
