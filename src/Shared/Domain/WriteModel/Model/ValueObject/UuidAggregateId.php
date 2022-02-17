<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
abstract class UuidAggregateId
{
    protected string $id;

    public function __construct(string $id)
    {
        if (preg_match('/[0-9a-f]{12}4[0-9a-f]{3}[89ab][0-9a-f]{15}/i', $id) === false) {
            throw new \InvalidArgumentException('AggregateId is not a valid UUID4.');
        }

        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function equals(self $compare): bool
    {
        return $this->id === $compare->id;
    }
}
