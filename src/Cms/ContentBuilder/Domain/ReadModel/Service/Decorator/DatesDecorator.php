<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

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
            'constraints' => [
                'required' => [],
            ],
        ]));
        $contentType->addField(new Field([
            'code' => 'published_to',
            'type' => 'datetime',
            'name' => 'publicationEndsAt',
        ]));
    }
}
