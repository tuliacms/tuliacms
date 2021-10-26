<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Validator\Constraint;

/**
 * @author Adam Banaszkiewicz
 */
class ConstraintTypeMappingRegistry
{
    private array $mapping = [
        'required' => [
            'classname' => 'Symfony\Component\Validator\Constraints\NotBlank',
        ],
    ];

    public function addMapping(string $type, array $mapingInfo): void
    {
        $this->mapping[$type] = $mapingInfo;
    }

    public function getConstraint(string $type, array $args = []): Constraint
    {
        return new $this->mapping[$type]['classname'](...$args);
    }

    public function hasType(string $type): bool
    {
        return isset($this->mapping[$type]);
    }
}
