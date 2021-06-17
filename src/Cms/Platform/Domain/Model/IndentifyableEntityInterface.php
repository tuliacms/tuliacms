<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\Model;

use Tulia\Cms\Platform\Domain\ValueObject\EntityIdInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface IndentifyableEntityInterface
{
    public function getId(): EntityIdInterface;
}
