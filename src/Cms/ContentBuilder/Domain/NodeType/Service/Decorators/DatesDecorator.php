<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorators;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
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
            'constraints' => [
                ['name' => 'required'],
            ],
        ]));
        $nodeType->addField(new Field([
            'name' => 'published_to',
            'type' => 'datetime',
            'label' => 'publicationEndsAt',
        ]));
    }
}
