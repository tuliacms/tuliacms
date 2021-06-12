<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function get(string $type): TypeInterface;
    public function add(TypeInterface $type): void;
    public function has(string $type): bool;
    public function all(): array;
}
