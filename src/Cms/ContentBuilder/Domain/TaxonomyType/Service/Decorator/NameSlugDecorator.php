<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NameSlugDecorator implements TaxonomyTypeDecoratorInterface
{
    public function decorate(TaxonomyType $taxonomyType): void
    {
        $taxonomyType->addField(new Field([
            'name' => 'title',
            'type' => 'text',
            'label' => 'title',
            'multilingual' => true,
            'internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        ['name' => 'required'],
                    ],
                ];
            }
        ]));
        if ($taxonomyType->isRoutable()) {
            $taxonomyType->addField(
                new Field([
                    'name' => 'slug',
                    'type' => 'text',
                    'label' => 'slug',
                    'multilingual' => true,
                    'internal' => true,
                    // @todo Create constraint for globally uniqueness of the slug
                    /*'constraints' => [
                        ['name' => 'unique', 'flags' => 'globally'],
                    ],*/
                ])
            );
        }
    }
}
