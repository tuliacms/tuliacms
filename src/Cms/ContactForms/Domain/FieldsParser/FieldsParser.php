<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldsParser;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface;
use Tulia\Component\Shortcode\Processor;
use Tulia\Component\Shortcode\Registry\CompilerRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsParser implements FieldsParserInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var array
     */
    private static $cache = [];

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
        $stream->setResult($result);

        self::$cache[$key]['result'] = $result;
        self::$cache[$key]['fields'] = $stream->allFields();

        return $stream;
    }

    private function replaceFieldsShortcodes(string $fieldsContent, array $fields): string
    {
        foreach ($fields as $field) {
            $shortcode = $this->createShortcode($field);
            $fieldsContent = str_replace("[{$field['name']}]", $shortcode, $fieldsContent);
        }

       return $fieldsContent;
    }

    private function createShortcode($field): string
    {
        $shortcode = sprintf('[%s name="%s"', $field['type'], $field['name']);

        foreach ($field['options'] as $option => $value) {
            if ($value === null) {
                continue;
            }

            $shortcode .= sprintf(' %s="%s"', $option, $value);
        }

        return $shortcode . ']';
    }
}
