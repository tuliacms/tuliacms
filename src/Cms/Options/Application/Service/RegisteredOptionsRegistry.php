<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

/**
 * @author Adam Banaszkiewicz
 */
class RegisteredOptionsRegistry
{
    private array $definitions;

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function collectRegisteredOptions(): array
    {
        return $this->definitions;
    }
}
