<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsParserInterface
{
    /**
     * @throws InvalidFieldNameException
     * @throws MultipleFieldsInTemplateException
     */
    public function parse(string $fieldsContent, array $fields): FieldsStreamInterface;
}
