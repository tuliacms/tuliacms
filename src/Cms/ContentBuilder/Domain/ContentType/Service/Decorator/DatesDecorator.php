<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatesDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'published_at',
            'type' => 'datetime',
            'name' => 'publishedAt',
            'is_internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        'required' => [],
                    ],
                ];
            }
        ]));
        $contentType->addField(new Field([
            'code' => 'published_to',
            'type' => 'datetime',
            'name' => 'publicationEndsAt',
            'is_internal' => true,
        ]));
    }
}
