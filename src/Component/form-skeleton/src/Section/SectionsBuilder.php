<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Section;

/**
 * @author Adam Banaszkiewicz
 */
class SectionsBuilder implements SectionsBuilderInterface
{
    protected $sections = [];

    public function add(string $id, array $data): SectionsBuilderInterface
    {
        $this->sections[$id] = array_merge([
            'id' => $id,
            'priority' => 0,
            'translation_domain' => 'messages',
            'label' => $id,
            'group' => 'default',
            'fields' => [$id],
        ], $data);

        return $this;
    }

    public function all(?string $group = null): array
    {
        $sections = [];

        foreach ($this->sections as $section) {
            if ($group === null) {
                $sections[] = $section;
            } elseif ($section['group'] === $group) {
                $sections[] = $section;
            }
        }

        usort($sections, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return $sections;
    }
}
