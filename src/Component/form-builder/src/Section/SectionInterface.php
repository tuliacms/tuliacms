<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionInterface
{
    public function getId(): string;
    public function setId(string $id): SectionInterface;
    public function getLabel(): ?string;
    public function setLabel(?string $label): SectionInterface;
    public function getTranslationDomain(): ?string;
    public function setTranslationDomain(?string $translationDomain): SectionInterface;
    public function getView(): ?string;
    public function setView(?string $view): SectionInterface;
    public function getPriority(): int;
    public function setPriority(int $priority): SectionInterface;
    public function getGroup(): string;
    public function setGroup(string $group): SectionInterface;
    public function getFields();
    public function setFields($fields): SectionInterface;
    public function getFieldsStatement(): string;
    public function getViewStatement(): string;
}
