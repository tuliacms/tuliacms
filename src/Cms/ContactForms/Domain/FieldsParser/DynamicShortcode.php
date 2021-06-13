<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\FieldParserInterface;
use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DynamicShortcode implements ShortcodeCompilerInterface
{
    private FieldParserInterface $fieldParser;

    private FieldsStreamInterface $fieldsStream;

    public function __construct(FieldsStreamInterface $fieldsStream, FieldParserInterface $fieldParser)
    {
        $this->fieldParser  = $fieldParser;
        $this->fieldsStream = $fieldsStream;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(ShortcodeInterface $shortcode): string
    {
        $fieldData = $this->fieldParser->parseShortcode($shortcode);
        $fieldData['type_alias'] = $this->fieldParser->getAlias();

        if (isset($fieldData['options']['constraints'])) {
            $fieldData['options']['constraints_raw'] = $shortcode->getParameter('constraints');
        }

        $this->fieldsStream->addField($fieldData['name'], $fieldData);

        return "{{ form_row(form.{$fieldData['name']}) }}";
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return $this->fieldParser->getAlias();
    }
}
