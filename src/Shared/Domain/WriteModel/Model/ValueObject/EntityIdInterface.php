<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
interface EntityIdInterface
{
    public function getValue(): string;

    public function __toString(): string;
}
