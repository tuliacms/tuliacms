<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldsParser;

use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsStream implements FieldsStreamInterface
{
    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $source;

    /**
     * @var null|string
     */
    private $result;

    /**
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function addField(string $name, array $field): void
    {
        if (! preg_match('/^[a-z0-9_]+$/i', $name)) {
            throw InvalidFieldNameException::fromName($name);
        }

        $this->fields[$name] = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function allFields(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function setResult(?string $result): void
    {
        $this->result = $result;
    }
}
