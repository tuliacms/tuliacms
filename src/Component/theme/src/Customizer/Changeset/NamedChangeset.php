<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

/**
 * @author Adam Banaszkiewicz
 */
class NamedChangeset implements NamedChangesetInterface
{
    /**
     * @var ChangesetInterface
     */
    protected $changeset;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $translationDomain = '';

    /**
     * @param ChangesetInterface $changeset
     */
    public function __construct(ChangesetInterface $changeset)
    {
        $this->changeset = $changeset;
    }

    /**
     * @return ChangesetInterface
     */
    public function getChangeset(): ChangesetInterface
    {
        return $this->changeset;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->changeset->getId();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }
}
