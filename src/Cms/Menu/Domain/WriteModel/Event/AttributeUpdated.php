<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeUpdated extends DomainEvent
{
    private string $name;
    private $value;

    public function __construct(string $userId, string $name, $value)
    {
        parent::__construct($userId);

        $this->name  = $name;
        $this->value = $value;
    }

    public static function fromModel(Item $item, string $name, $value): self
    {
        return new self($item->getId(), $name, $value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }
}
