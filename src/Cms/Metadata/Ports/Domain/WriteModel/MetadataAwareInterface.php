<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Ports\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface MetadataAwareInterface
{
    public function __get(string $name);

    public function __set(string $name, $value): void;

    public function __isset(string $name): bool;

    public function meta(string $name, $default = null);

    public function getAllMetadata(): array;

    public function replaceMetadata(array $metadata): void;
}
