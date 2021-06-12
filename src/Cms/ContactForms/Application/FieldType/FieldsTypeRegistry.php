<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\FieldParserInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTypeRegistry implements FieldsTypeRegistryInterface
{
    /**
     * @var TypeInterface[]
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
    public function get(string $type): TypeInterface
    {
        $this->prepareTypes();

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
