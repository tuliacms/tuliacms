<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSection implements SectionInterface
{
    protected string $id;

    protected ?string $label = null;

    protected ?string $translationDomain = null;

    protected ?string $view = null;

    protected int $priority = 0;

    protected string $group = 'default';

    /**
     * @var string|array
     */
    protected $fields = '';

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): SectionInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel(?string $label): SectionInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain(?string $translationDomain): SectionInterface
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function setView(?string $view): SectionInterface
    {
        $this->view = $view;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(int $priority): SectionInterface
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(string $group): SectionInterface
    {
        $this->group = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function setFields($fields): SectionInterface
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsStatement(): string
    {
        if (\is_array($this->fields)) {
            return "{% set fields = ['" . implode("', '", $this->fields) . "'] %}";
        } elseif (\is_string($this->fields)) {
            return $this->fields;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getViewStatement(): string
    {
        if (strncmp($this->view, '@', 1) === 0) {
            return "{% include '{$this->view}' %}";
        }

        return $this->view;
    }
}
