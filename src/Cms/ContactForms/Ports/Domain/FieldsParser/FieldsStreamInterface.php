<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Ports\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\InvalidFieldNameException;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsStreamInterface
{
    /**
     * @throws InvalidFieldNameException
     */
    public function addField(string $name, array $field): void;

    public function allFields(): array;

    public function getResult(): ?string;

    public function setResult(?string $result): void;
}
