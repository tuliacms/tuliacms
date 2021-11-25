<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeUpdated extends DomainEvent
{
    private string $attribute;
    private $value;

    public static function fromNode(Node $node, string $attribute, $value): self
    {
        return new self(
            $node->getId()->getId(),
            $node->getType(),
            $node->getWebsiteId(),
            $node->getLocale(),
            $attribute,
            $value,
        );
    }

    public function __construct(string $nodeId, string $nodeType, string $websiteId, string $locale, string $attribute, $value)
    {
        parent::__construct($nodeId, $nodeType, $websiteId, $locale);

        $this->attribute = $attribute;
        $this->value = $value;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
