<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Ports\Domain\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsTypeRegistryInterface
{
    public function get(string $type): FieldTypeInterface;

    public function getParser(string $type): FieldParserInterface;

    public function has(string $type): bool;

    /**
     * @return FieldTypeInterface[]
     */
    public function all(): array;
}
