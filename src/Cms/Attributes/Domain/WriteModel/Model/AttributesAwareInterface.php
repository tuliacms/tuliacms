<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
interface AttributesAwareInterface
{
    public function __get(string $name);

    public function __set(string $name, $value): void;

    public function __isset(string $name): bool;

    public function attribute(string $name, $default = null);

    public function getAttributes(): array;

    public function replaceAttributes(array $metadata): void;
}
