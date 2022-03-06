<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
class ImageSize
{
    private string $name;
    private string $label;
    private ?int $width;
    private ?int $height;
    private string $mode;
    private ?string $translationDomain;

    public function __construct(
        string $name,
        string $label,
        ?int $width = null,
        ?int $height = null,
        string $mode = 'fit',
        ?string $translationDomain = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->width = $width;
        $this->height = $height;
        $this->mode = $mode;
        $this->translationDomain = $translationDomain;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['name'], $data['label'], $data['width'], $data['height'], $data['mode'], $data['translation_domain']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function getCode(): string
    {
        return sprintf('%s_%dx%d', $this->name, $this->width, $this->height);
    }
}
