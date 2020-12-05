<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    public function getMany(string $type, $elementId, array $names): array;
    public function set(string $type, $elementId, string $name, $value, bool $multilingual = false): void;
    public function insert(string $type, $elementId, string $name, $value, bool $multilingual = false): void;
    public function delete(string $type, $elementId, string $name): void;
    public function deleteAll(string $type, $elementId): void;
}
