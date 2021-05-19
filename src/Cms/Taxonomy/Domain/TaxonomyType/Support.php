<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Domain\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
class Support implements SupportInterface
{
    protected $type;
    protected $name;
    protected $translationDomain = 'messages';

    public function __construct(string $type)
    {
        $this->type = $this->name = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain)
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }
}
