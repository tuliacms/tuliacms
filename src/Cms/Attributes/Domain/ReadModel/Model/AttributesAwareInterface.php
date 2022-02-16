<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\ReadModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesAwareInterface extends \ArrayAccess
{
    public function attribute(string $name, $default = null);
    public function getAttributes(): array;
    public function replaceAttributes(array $attributes): void;
    public function mergeAttributes(array $attributes): void;
}
