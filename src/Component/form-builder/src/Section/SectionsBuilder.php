<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
class SectionsBuilder implements SectionsBuilderInterface
{
    protected $sections = [];

    public function add(SectionInterface $section): SectionInterface
    {
        return $this->sections[$section->getId()] = $section;
    }

    public function section(string $type): SectionInterface
    {
        // TODO: Implement section() method.
    }

    public function rowSection(string $id, string $label = null, $field = null): SectionInterface
    {
        return $this->add(new FormRowSection($id, $label ?? $id, $field ?? $id));
    }

    public function all(?string $group = null): array
    {
        $sections = [];

        foreach ($this->sections as $section) {
            if ($group === null) {
                $sections[] = $section;
            } elseif ($section->getGroup() === $group) {
                $sections[] = $section;
            }
        }

        usort($sections, function ($a, $b) {
            return $b->getPriority() - $a->getPriority();
        });

        return $sections;
    }
}
