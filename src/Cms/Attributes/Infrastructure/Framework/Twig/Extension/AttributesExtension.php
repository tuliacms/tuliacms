<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Attributes\Domain\ReadModel\Model\AttributeValue;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('empty', function ($value) {
                if ($value instanceof AttributeValue) {
                    return $value->isEmpty();
                }

                return empty($value);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
