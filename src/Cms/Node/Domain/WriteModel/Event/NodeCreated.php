<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class NodeCreated extends DomainEvent
{
    private string $type;

    public function __construct(string $nodeId, string $nodeType, string $websiteId, string $locale, string $type)
    {
        parent::__construct($nodeId, $nodeType, $websiteId, $locale);

        $this->type = $type;
    }

    public static function fromNode(Node $node): self
    {
        return new self($node->getId()->getId(), $node->getType(), $node->getWebsiteId(), $node->getLocale(), $node->getType());
    }

    public function getType(): string
    {
        return $this->type;
    }
}
