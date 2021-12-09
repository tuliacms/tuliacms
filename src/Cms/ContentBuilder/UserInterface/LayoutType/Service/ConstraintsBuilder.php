<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Symfony\Component\Validator\Constraint;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\ConstraintNotExistsException;

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

    /**
     * @throws ConstraintNotExistsException
     */
    public function build(array $constraints): array
    {
        if ($constraints === []) {
            return [];
        }

        $result = [];

        foreach ($constraints as $constraint) {
            if ($constraint instanceof Constraint) {
                $result[] = $constraint;
            } else {
                $modificators = [];

                foreach ($constraint['modificators'] ?? [] as $modificator) {
                    $modificators[$modificator['modificator']] = $modificator['value'];
                }

                $result[] = $this->mapping->getConstraint($constraint['name'], [$modificators]);
            }
        }

        return $result;
    }
}
