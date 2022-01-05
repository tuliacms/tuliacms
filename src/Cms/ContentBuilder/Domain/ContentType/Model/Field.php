<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Field
{
    private array $options;
    private array $builderOptions = [];
    private static $defaults = [
        'code' => '',
        'type' => '',
        'name' => '',
        'is_multilingual' => false,
        'is_multiple' => false,
        'is_internal' => false,
        'flags' => [],
        'configuration' => [],
        'constraints' => [],
        'builder_options' => null,
    ];

    public function __construct(array $options) {
        $this->options = array_merge(static::$defaults, $options);

        \assert(\is_string($this->options['code']), 'The "code" option must be a string.');
        \assert(\is_string($this->options['type']), 'The "type" option must be a string.');
        \assert(\is_string($this->options['name']), 'The "name" option must be a string.');
        \assert(\is_bool($this->options['is_multilingual']), 'The "is_multilingual" option must be a boolean.');
        \assert(\is_bool($this->options['is_multiple']), 'The "is_multiple" option must be a boolean.');
        \assert(\is_bool($this->options['is_internal']), 'The "internal" option must be a boolean.');
        \assert(\is_array($this->options['flags']), 'The "flags" option must be an array.');
        \assert(\is_array($this->options['configuration']), 'The "configuration" option must be an array.');
        \assert(\is_array($this->options['constraints']), 'The "constraints" option must be an array.');
        \assert($this->options['builder_options'] === null || \is_callable($this->options['builder_options']), 'The "builder_options" option must be an array.');

        if ($this->options['type'] === 'taxonomy') {
            \assert(\is_string($this->options['taxonomy']), 'The "taxonomy" option must be a string.');
        }

        foreach ($this->options['constraints'] as $constraintName => $constraint) {
            \assert(\is_array($constraint), sprintf('Constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

            if (isset($constraint['modificators'])) {
                \assert(\is_array($constraint['modificators']), sprintf('Modificators of constraint "%s" of field "%s" must be an array.', $constraintName, $this->options['code']));

                foreach ($constraint['modificators'] as $name => $value) {
                    \assert(
                        \is_string($value) || is_numeric($value) || $value === null,
                        sprintf('Value of modificator %s of constraint "%s" of field "%s" must be a simple, non array value.', $name, $constraintName, $this->options['code'])
                    );
                }
            }
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

    public function isMultilingual(): bool
    {
        return $this->options['is_multilingual'];
    }

    public function isMultiple(): bool
    {
        return $this->options['is_multiple'];
    }

    public function isInternal(): bool
    {
        return $this->options['is_internal'];
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

    public function hasFlag(string $flag): bool
    {
        return in_array($flag, $this->options['flags'], true);
    }

    public function getBuilderOptions(): array
    {
        if ($this->builderOptions !== []) {
            return $this->builderOptions;
        }

        if (is_callable($this->options['builder_options'])) {
            return $this->options['builder_options'] = $this->options['builder_options']();
        }

        return [];
    }

    public function getTaxonomy(): string
    {
        if ($this->options['type'] !== 'taxonomy') {
            throw new \LogicException('Cannot get "taxonomy" from field that is not a taxonomy type.');
        }
        return $this->options['taxonomy'];
    }
}
