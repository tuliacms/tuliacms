<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleEntityId implements EntityIdInterface
{
    protected string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
