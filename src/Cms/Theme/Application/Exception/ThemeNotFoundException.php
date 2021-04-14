<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Application\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeNotFoundException extends ApplicationException
{
    public static function withName(string $name): self
    {
        return new self(sprintf('Theme %s not exists.', $name));
    }
}
