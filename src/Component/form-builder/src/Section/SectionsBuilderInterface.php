<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionsBuilderInterface
{
    public function add(SectionInterface $section): SectionInterface;

    public function section(string $type): SectionInterface;

    public function rowSection(string $id, string $label = null, $field = null): SectionInterface;

    /**
     * @return SectionInterface[]
     */
    public function all(?string $group = null): array;
}
