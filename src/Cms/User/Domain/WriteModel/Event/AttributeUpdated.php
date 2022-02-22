<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\User\Domain\WriteModel\Model\User;

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

    public static function fromModel(User $user, string $name, $value): self
    {
        return new self($user->getId()->getValue(), $name, $value);
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
