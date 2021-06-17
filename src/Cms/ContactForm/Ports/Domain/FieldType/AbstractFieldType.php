<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Ports\Domain\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFieldType implements FieldTypeInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getFormType(): string;

    /**
     * {@inheritdoc}
     */
    public function getBuilder(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options): array
    {
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFromRequest($value, array $options)
    {
        return $value;
    }
}
