<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorator;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class StatusDecorator implements NodeTypeDecoratorInterface
{
    public function decorate(NodeType $nodeType): void
    {
        $nodeType->addField(new Field([
            'code' => 'status',
            'type' => 'select',
            'name' => 'publicationStatus',
            'internal' => true,
            'constraints' => [
                'required' => [],
            ],
            'builder_options' => function () {
                return [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Choice([ 'choices' => ['draft', 'published', 'trashed'] ]),
                    ],
                    'choices' => [
                        'Draft' => 'draft',
                        'Published' => 'published',
                        'Trashed' => 'trashed',
                    ],
                ];
            }
        ]));
    }
}
