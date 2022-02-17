<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldTypeInterface
{
    public function getAlias(): string;

    public function getFormType(): string;

    public function getBuilder(): string;

    public function buildOptions(array $options): array;

    /**
     * Takes as argument value from request, and transforms
     * it to show in response or in Mail message. Used by Choice
     * to replace chice ID to choice value.
     *
     * @param mixed $value
     * @param array $options
     *
     * @return mixed
     */
    public function prepareValueFromRequest($value, array $options);
}
