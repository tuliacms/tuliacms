<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\AbstractModel;

/**
 * @author Adam Banaszkiewicz
 */
class AbstractField
{
    protected array $options;
    protected static $defaults = [
        'code' => '',
        'type' => '',
        'name' => '',
        'taxonomy' => '',
        'is_multilingual' => false,
        'has_nonscalar_value' => false,
        'flags' => [],
        'configuration' => [],
        'constraints' => [],
        'children' => [],
    ];

    public function __construct(array $options) {
        $this->options = array_merge(static::$defaults, $options);

        \assert(\is_string($this->options['code']), 'The "code" option must be a string.');
        \assert(\is_string($this->options['type']), 'The "type" option must be a string.');
        \assert(\is_string($this->options['name']), 'The "name" option must be a string.');
        \assert(\is_bool($this->options['is_multilingual']), 'The "is_multilingual" option must be a boolean.');
        \assert(\is_bool($this->options['has_nonscalar_value']), 'The "has_nonscalar_value" option must be a boolean.');
        \assert(\is_array($this->options['flags']), 'The "flags" option must be an array.');
        \assert(\is_array($this->options['configuration']), 'The "configuration" option must be an array.');
        \assert(\is_array($this->options['constraints']), 'The "constraints" option must be an array.');
        \assert(\is_array($this->options['children']), 'The "children" option must be an array.');

        if ($this->options['type'] === 'taxonomy') {
            \assert(\is_string($this->options['taxonomy']), 'The "taxonomy" option must be a string.');
        }

        foreach ($this->options['children'] as $child) {
            \assert(is_object($child) && $child instanceof AbstractField, 'The children must be a Field instance.');
        }

        foreach ($this->options['constraints'] as $constraintName => $constraint) {
            \assert(\is_array($constraint), sprintf('Constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

            if (isset($constraint['modificators'])) {
                \assert(\is_array($constraint['modificators']), sprintf('Modificators of constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

                foreach ($constraint['modificators'] as $name => $value) {
                    \assert(
                        \is_string($value) || is_numeric($value) || $value === null,
                        sprintf('Value of modificator "%s" of constraint "%s" of field "%s" must be a scalar value.', $name, $constraintName, $this->options['code'])
                    );
                }
            }
        }

        foreach ($this->options['configuration'] as $configName => $configValue) {
            \assert(
                \is_scalar($configValue),
                sprintf('Value of configuration "%s" of field "%s" must be a scalar value.', $configName, $this->options['code'])
            );
        }
    }

    public function getCode(): string
    {
        return $this->options['code'];
    }

    public function getType(): string
    {
        return $this->options['type'];
    }

    public function isType(string $type): bool
    {
        return $this->options['type'] === $type;
    }

    public function isMultilingual(): bool
    {
        return $this->options['is_multilingual'];
    }

    public function getName(): string
    {
        return $this->options['name'];
    }

    public function getConfiguration(): array
    {
        return $this->options['configuration'];
    }

    public function getConfig(string $name, $default = null)
    {
        return $this->options['configuration'][$name] ?? $default;
    }

    public function getConstraints(): array
    {
        return $this->options['constraints'];
    }

    /**
     * @return AbstractField[]
     */
    public function getChildren(): array
    {
        return $this->options['children'];
    }

    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->options['flags'], true);
    }

    /**
     * @return string[]
     */
    public function getFlags(): array
    {
        return $this->options['flags'];
    }

    public function hasNonscalarValue(): bool
    {
        return $this->options['has_nonscalar_value'];
    }

    public function getTaxonomy(): string
    {
        return $this->options['taxonomy'];
    }
}
