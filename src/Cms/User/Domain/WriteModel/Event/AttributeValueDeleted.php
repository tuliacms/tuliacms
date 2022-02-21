<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeValueDeleted extends DomainEvent
{
    private string $name;

    private $value;

    public function __construct(string $userId, string $name, $value)
    {
        parent::__construct($userId);

        $this->name  = $name;
        $this->value = $value;
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
