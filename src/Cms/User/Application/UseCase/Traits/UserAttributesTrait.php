<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase\Traits;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;

/**
 * @author Adam Banaszkiewicz
 */
trait UserAttributesTrait
{
    /**
     * @param Attribute[] $attributes
     */
    protected function flattenAttributes(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $attribute) {
            if ($attribute instanceof Attribute) {
                $result[$attribute->getUri()] = $attribute->getValue();
            }
        }

        return $result;
    }

    /**
     * @param Attribute[] $attributes
     * @return Attribute[]
     */
    protected function removeModelsAttributes(array $attributes): array
    {
        unset(
            $attributes['password'],
            $attributes['password_repeat'],
            $attributes['email'],
            $attributes['roles'],
            $attributes['enabled'],
            $attributes['locale'],
        );

        return $attributes;
    }
}
