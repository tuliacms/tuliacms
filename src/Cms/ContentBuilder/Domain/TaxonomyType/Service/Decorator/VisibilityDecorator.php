<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class VisibilityDecorator implements TaxonomyTypeDecoratorInterface
{
    public function decorate(TaxonomyType $taxonomyType): void
    {
        $taxonomyType->addField(new Field([
            'name' => 'visibility',
            'type' => 'yes_no',
            'label' => 'visibility',
            'multilingual' => true,
            'internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        'required' => [],
                    ],
                ];
            }
        ]));
    }
}
