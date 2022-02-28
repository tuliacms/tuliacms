<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Structure;

/**
 * @author Adam Banaszkiewicz
 */
class Control
{
    private string $code;
    private string $type;
    private ?string $label;
    private $defaultValue;
    private string $transport = 'refresh';
    private bool $isMultilingual = false;
    private array $options = [];

    public function __construct(string $code, string $type = 'text', ?string $label = null, $defaultValue = null)
    {
        $this->code = $code;
        $this->type = $type;
        $this->label = $label;
        $this->defaultValue = $defaultValue;
    }

    public static function fromArray(array $data): self
    {
        $self = new self($data['code'], $data['type'], $data['label'], $data['value']);
        $self->transport = $data['transport'];
        $self->isMultilingual = $data['multilingual'];
        $self->options = $data['options'];

        return $self;
    }

    public function toArray(): array
    {
        return $this->options + [
            'code' => $this->code,
            'type' => $this->type,
            'label' => $this->label,
            'default_value' => $this->defaultValue,
            'transport' => $this->transport,
            'is_multilingual' => $this->isMultilingual,
        ];
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function getTransport(): string
    {
        return $this->transport;
    }

    public function isMultilingual(): bool
    {
        return $this->isMultilingual;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
