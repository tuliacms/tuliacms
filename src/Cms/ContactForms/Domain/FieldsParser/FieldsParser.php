<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface;
use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Component\Shortcode\Processor;
use Tulia\Component\Shortcode\Registry\CompilerRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsParser implements FieldsParserInterface
{
    private const REST_FIELDS_SEPARATOR = '<!--REST_FIELDS-->';

    private RegistryInterface $registry;

    private static array $cache = [];

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $fieldsContent, array $fields): FieldsStreamInterface
    {
        $key = md5($fieldsContent);

        if (isset(self::$cache[$key])) {
            $stream = new FieldsStream($fieldsContent);
            $stream->setResult(self::$cache[$key]['result']);

            foreach (self::$cache[$key]['fields'] as $name => $val) {
                $stream->addField($name, $val);
            }

            return $stream;
        }

        $fieldsContent = $this->replaceFieldsShortcodes($fieldsContent, $fields);

        $compilers = new CompilerRegistry();
        $stream = new FieldsStream($fieldsContent);

        foreach ($this->registry->all() as $parser) {
            $compilers->add(new DynamicShortcode($stream, $parser));
        }

        $processor = new Processor($compilers);
        $result = $processor->process($fieldsContent);

        [$result, $restFields] = explode(self::REST_FIELDS_SEPARATOR, $result);

        $stream->setResult($result);

        self::$cache[$key]['result'] = $result;
        self::$cache[$key]['fields'] = $stream->allFields();

        return $stream;
    }

    /**
     * @throws MultipleFieldsInTemplateException
     */
    private function replaceFieldsShortcodes(string $fieldsContent, array $fields): string
    {
        $restFields = '';
        $replacements = [];

        foreach ($fields as $field) {
            $tag = "[{$field['name']}]";
            $replacements[$tag] = $this->createShortcode($field);

            if (substr_count($fieldsContent, $tag) > 1) {
                throw MultipleFieldsInTemplateException::fromName($tag);
            }

            if (! strpos($fieldsContent, $tag)) {
                $restFields .= $tag;
            }
        }

        $fieldsContent .= self::REST_FIELDS_SEPARATOR . $restFields;
        $fieldsContent = str_replace(array_keys($replacements), array_values($replacements), $fieldsContent);

        return $fieldsContent;
    }

    private function createShortcode(array $field): string
    {
        $shortcode = sprintf('[%s name="%s"', $field['type'], $field['name']);

        foreach ($field['options'] as $option => $value) {
            if ($value === null || $option === 'constraints_raw') {
                continue;
            }

            $shortcode .= sprintf(' %s="%s"', $option, $value);
        }

        return $shortcode . ']';
    }
}
