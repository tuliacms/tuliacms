<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorators;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
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
        if ($nodeType->isRoutable()) {
            $nodeType->addField(
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
