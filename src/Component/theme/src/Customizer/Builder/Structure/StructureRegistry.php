<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Structure;

/**
 * @author Adam Banaszkiewicz
 */
class StructureRegistry
{
    private array $structureByThemes;

    public function addForTheme(string $theme, array $structure)
    {
        $this->structureByThemes[$theme] = $structure;
    }

    /**
     * @return Section[]
     */
    public function get(string $theme): array
    {
        if (isset($this->structureByThemes[$theme]) === false) {
            return [];
        }

        $result = [];

        foreach ($this->structureByThemes[$theme] as $section) {
            $result[] = Section::fromArray($section);
        }

        return $result;
    }
}
