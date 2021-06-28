<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class FlagNotFoundException extends \Exception
{
    public static function fromName(string $name): self
    {
        return new self(sprintf('Flag "%s" not found.', $name));
    }
}
