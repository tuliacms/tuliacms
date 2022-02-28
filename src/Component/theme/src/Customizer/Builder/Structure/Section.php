<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Structure;

/**
 * @author Adam Banaszkiewicz
 */
class Section
{
    private string $code;
    private string $label;
    private array $controls = [];
    private ?string $parent;
    private string $transationDomain = 'messages';

    public function __construct(string $code, string $label, array $controls = [], ?string $parent = null)
    {
        $this->code = $code;
        $this->label = $label;
        $this->parent = $parent;

        foreach ($controls as $control) {
            $this->controls[] = Control::fromArray($control);
        }
    }

    public static function fromArray(array $data): self
    {
        $self = new self($data['code'], $data['label'], $data['controls'], $data['parent']);
        $self->transationDomain = $data['translation_domain'];

        return $self;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return Control[]
     */
    public function getControls(): array
    {
        return $this->controls;
    }

    public function setControls(array $controls): void
    {
        $this->controls = $controls;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function getTransationDomain(): string
    {
        return $this->transationDomain;
    }
}
