<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\Model\Entity;

use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\EntityIdInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface IndentifyableEntityInterface
{
    public function getId(): EntityIdInterface;
}
