<?php

declare(strict_types=1);

namespace Tulia\Cms\Options;

use Tulia\Cms\Options\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface OptionsInterface
{
    public function setStorage(StorageInterface $storage): void;
    public function getStorage(): StorageInterface;
    public function get(string $name, $default = null);
    public function set(string $name, $value): void;
    public function has(string $name): bool;
    public function remove(string $name): void;
    public function rename(string $old, string $new): void;
    public function create(string $name, $value, bool $multilingual = false, bool $autoload = null): void;
    public function preload(array $names = []): void;
}
