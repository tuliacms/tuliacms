<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\InvalidFieldNameException;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsStreamInterface
{
    /**
     * @param string $name
     * @param array $field
     * @throws InvalidFieldNameException
     */
    public function addField(string $name, array $field): void;

    /**
     * @return array
     */
    public function allFields(): array;

    /**
     * @return string|null
     */
    public function getResult(): ?string;

    /**
     * @param string|null $result
     */
    public function setResult(?string $result): void;
}
