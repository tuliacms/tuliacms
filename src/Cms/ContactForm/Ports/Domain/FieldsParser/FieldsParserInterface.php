<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Ports\Domain\FieldsParser;

use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Cms\ContactForm\Ports\Domain\FieldsParser\FieldsStreamInterface;

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
