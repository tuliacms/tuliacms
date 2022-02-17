<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType;

use Tulia\Cms\ContactForm\Domain\Exception\FieldTypeNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsTypeRegistryInterface
{
    /**
     * @throws FieldTypeNotFoundException
     */
    public function get(string $type): FieldTypeInterface;

    public function getParser(string $type): FieldParserInterface;

    public function has(string $type): bool;

    /**
     * @return FieldTypeInterface[]
     */
    public function all(): array;
}
