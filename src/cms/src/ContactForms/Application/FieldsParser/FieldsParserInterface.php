<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldsParser;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsParserInterface
{
    public function parse(string $fieldsContent): FieldsStreamInterface;
}
