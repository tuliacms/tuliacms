<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\FieldParserInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsTypeRegistryInterface
{
    public function get(string $type): TypeInterface;
    public function getParser(string $type): FieldParserInterface;
    public function has(string $type): bool;
    public function all(): array;
}
