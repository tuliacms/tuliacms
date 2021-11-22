<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\Field\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Field
{
    private array $options;
    private array $builderOptions = [];

    private static $defaults = [
        'name' => '',
        'type' => '',
        'label' => '',
        'multilingual' => false,
        'multiple' => false,
        'internal' => false,
        'flags' => [],
        'builder_options' => null,
    ];

    public function __construct(array $options) {
        $this->options = array_merge(static::$defaults, $options);

        \assert(\is_string($this->options['name']), 'The "name" option must be a string.');
        \assert(\is_string($this->options['type']), 'The "type" option must be a string.');
        \assert(\is_string($this->options['label']), 'The "label" option must be a string.');
        \assert(\is_bool($this->options['multilingual']), 'The "multilingual" option must be a boolean.');
        \assert(\is_bool($this->options['multiple']), 'The "multiple" option must be a boolean.');
        \assert(\is_bool($this->options['internal']), 'The "internal" option must be a boolean.');
        \assert(\is_array($this->options['flags']), 'The "flags" option must be an array.');
        \assert($this->options['builder_options'] === null || \is_callable($this->options['builder_options']), 'The "builder_options" option must be an array.');

        if ($this->options['type'] === 'taxonomy') {
            \assert(\is_string($this->options['taxonomy']), 'The "taxonomy" option must be a string.');
        }
    }

    public function getName(): string
    {
        return $this->options['name'];
    }

    public function getType(): string
    {
        return $this->options['type'];
    }

    public function isMultilingual(): bool
    {
        return $this->options['multilingual'];
    }

    public function isMultiple(): bool
    {
        return $this->options['multiple'];
    }

    public function isInternal(): bool
    {
        return $this->options['internal'];
    }

    public function getLabel(): string
    {
        return $this->options['label'];
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
