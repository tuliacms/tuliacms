<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractType implements TypeInterface
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
