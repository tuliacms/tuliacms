<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatesDecorator implements NodeTypeDecoratorInterface
{
    public function decorate(NodeType $nodeType): void
    {
        $nodeType->addField(new Field([
            'name' => 'published_at',
            'type' => 'datetime',
            'label' => 'publishedAt',
            'internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        ['name' => 'required'],
                    ],
                ];
            }
        ]));
        $nodeType->addField(new Field([
            'name' => 'published_to',
            'type' => 'datetime',
            'label' => 'publicationEndsAt',
            'internal' => true,
        ]));
    }
}
