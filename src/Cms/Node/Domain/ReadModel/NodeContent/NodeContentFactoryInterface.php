<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeContentFactoryInterface
{
    public function createForNode(Node $node): NodeContentInterface;
}
