<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Validator\Constraint;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\ConstraintNotExistsException;

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

    public function get(string $type): array
    {
        return $this->mapping[$type];
    }

    /**
     * @throws ConstraintNotExistsException
     */
    public function getConstraint(string $type, array $args = []): Constraint
    {
        if (isset($this->mapping[$type]['classname']) === false) {
            throw ConstraintNotExistsException::fromName($type);
        }

        dump($this->mapping[$type]['classname'], $args);
        return new $this->mapping[$type]['classname'](...$args);
    }

    public function hasType(string $type): bool
    {
        return isset($this->mapping[$type]);
    }
}
