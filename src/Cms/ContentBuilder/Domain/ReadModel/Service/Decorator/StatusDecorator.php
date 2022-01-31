<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class StatusDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'status',
            'type' => 'select',
            'name' => 'publicationStatus',
            'is_internal' => true,
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
