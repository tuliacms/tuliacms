<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Parser;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function get(string $parser): FieldParserInterface;
    public function add(FieldParserInterface $parser): void;
    public function has(string $parser): bool;
    public function all(): array;
}
