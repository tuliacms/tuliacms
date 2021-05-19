<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class NodeDeleted extends DomainEvent
{
    public static function fromNode(Node $node): self
    {
        return new self($node->getId()->getId(), $node->getWebsiteId(), $node->getLocale());
    }
}
