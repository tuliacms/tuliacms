<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType;

use Tulia\Cms\ContactForm\Domain\Exception\FieldTypeNotFoundException;
use Tulia\Cms\ContactForm\Ports\Domain\FieldType\FieldParserInterface;
use Tulia\Cms\ContactForm\Ports\Domain\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForm\Ports\Domain\FieldType\FieldTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTypeRegistry implements FieldsTypeRegistryInterface
{
    /**
     * @var FieldTypeInterface[]
     */
    private array $types = [];

    /**
     * @var FieldParserInterface[]
     */
    private array $parsers = [];

    private iterable $sourceTypes;

    private iterable $sourceParsers;

    public function __construct(iterable $sourceTypes, iterable $sourceParsers)
    {
        $this->sourceTypes = $sourceTypes;
        $this->sourceParsers = $sourceParsers;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $type): FieldTypeInterface
    {
        $this->prepareTypes();

        if (isset($this->types[$type]) === false) {
            throw FieldTypeNotFoundException::fromType($type);
        }

        return $this->types[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getParser(string $type): FieldParserInterface
    {
        $this->prepareTypes();

        return $this->parsers[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type): bool
    {
        $this->prepareTypes();

        return isset($this->types[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $this->prepareTypes();

        return $this->types;
    }

    protected function prepareTypes(): void
    {
        if ($this->types === []) {
            foreach ($this->sourceTypes as $type) {
                $this->types[$type->getAlias()] = $type;
            }
        }

        if ($this->parsers === []) {
            foreach ($this->sourceParsers as $parser) {
                $this->parsers[$parser->getAlias()] = $parser;
            }
        }
    }
}
