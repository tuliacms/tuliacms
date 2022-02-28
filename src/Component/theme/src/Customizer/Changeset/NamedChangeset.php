<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

/**
 * @author Adam Banaszkiewicz
 */
class NamedChangeset extends Changeset
{
    private string $label = '';
    private string $translationDomain = 'messages';
    private ?string $description = null;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
