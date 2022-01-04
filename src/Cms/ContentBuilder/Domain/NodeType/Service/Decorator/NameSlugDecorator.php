<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NameSlugDecorator implements NodeTypeDecoratorInterface
{
    public function decorate(NodeType $nodeType): void
    {
        $nodeType->addField(new Field([
            'code' => 'title',
            'type' => 'text',
            'name' => 'title',
            'is_multilingual' => true,
            'is_internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        'required' => [],
                    ],
                ];
            }
        ]));

        if ($nodeType->isRoutable()) {
            $nodeType->addField(
                new Field([
                    'code' => 'slug',
                    'type' => 'text',
                    'name' => 'slug',
                    'is_multilingual' => true,
                    'is_internal' => true,
                    // @todo Create constraint for globally uniqueness of the slug
                    /*'constraints' => [
                        ['name' => 'unique', 'flags' => 'globally'],
                    ],*/
                ])
            );
        }
    }
}
