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
    /**
     * @var FieldParserInterface
     */
    private $fieldParser;

    /**
     * @var FieldsStreamInterface
     */
    private $fieldsStream;

    /**
     * @param FieldsStreamInterface $fieldsStream
     * @param FieldParserInterface $fieldParser
     */
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

        $this->fieldsStream->addField($fieldData['name'], $fieldData);

        return "{{ form_row(form.{$fieldData['name']}) }}";
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->fieldParser->getName();
    }
}
