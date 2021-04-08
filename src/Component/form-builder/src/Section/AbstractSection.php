<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSection implements SectionInterface
{
    /**
     * @var null|string
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $label;

    /**
     * @var null|string
     */
    protected $translationDomain;

    /**
     * @var null|string
     */
    protected $view;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var string
     */
    protected $group = 'default';

    /**
     * @var string|array
     */
    protected $fields = '';

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
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
    public function setLabel(?string $label): void
    {
        $this->label = $label;
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
    public function setTranslationDomain(?string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
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
    public function setView(?string $view): void
    {
        $this->view = $view;
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
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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
    public function setGroup(string $group): void
    {
        $this->group = $group;
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
    public function setFields($fields): void
    {
        $this->fields = $fields;
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
