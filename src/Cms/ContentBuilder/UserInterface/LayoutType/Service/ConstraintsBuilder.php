<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

/**
 * @author Adam Banaszkiewicz
 */
class ConstraintsBuilder
{
    private ConstraintTypeMappingRegistry $mapping;

    public function __construct(ConstraintTypeMappingRegistry $mapping)
    {
        $this->mapping = $mapping;
    }

    public function build(array $constraints): array
    {
        if ($constraints === []) {
            return [];
        }

        $result = [];

        foreach ($constraints as $constraint) {
            $result[] = $this->mapping->getConstraint($constraint['name']);
        }

        return $result;
    }
}
