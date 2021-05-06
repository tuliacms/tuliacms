<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Option
{
    private ?string $id = null;

    private string $websiteId;

    private string $name;

    private ?string $locale;

    private $value;

    private bool $multilingual;

    private bool $autoload;

    public function __construct(
        string $websiteId,
        string $name,
        $value,
        ?string $locale = null,
        bool $multilingual = false,
        bool $autoload = false
    ) {
        $this->websiteId = $websiteId;
        $this->name = $name;
        $this->value = $value;
        $this->locale = $locale;
        $this->multilingual = $multilingual;
        $this->autoload = $autoload;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }

    public function setMultilingual(bool $multilingual): void
    {
        $this->multilingual = $multilingual;
    }

    public function isAutoload(): bool
    {
        return $this->autoload;
    }

    public function setAutoload(bool $autoload): void
    {
        $this->autoload = $autoload;
    }
}
