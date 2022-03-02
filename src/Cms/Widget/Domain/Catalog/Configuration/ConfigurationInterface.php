<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
interface ConfigurationInterface extends \ArrayAccess, \IteratorAggregate
{
    public function getSpace(): ?string;
    public function setSpace(?string $space): void;
    public function multilingualFields(array $fields): void;
    public function getMultilingualFields(): array;
    public function isMultilingual(string $name): bool;
    public function get(string $name, $default = null);
    public function set(string $name, $value): void;
    public function has(string $name): bool;
    public function all(): array;
    public function allMultilingual(): array;
    public function allNotMultilingual(): array;
    public function defaults(array $defaults = []): void;
    public function merge(array $import = []): void;
}
