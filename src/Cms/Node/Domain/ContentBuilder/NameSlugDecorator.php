<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ContentBuilder;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NameSlugDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType(['node']) === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'title',
            'type' => 'text',
            'name' => 'title',
            'is_multilingual' => true,
            'constraints' => [
                'required' => [],
            ],
        ]));

        if ($contentType->isRoutable()) {
            $contentType->addField(
                new Field([
                    'code' => 'slug',
                    'type' => 'text',
                    'name' => 'slug',
                    'is_multilingual' => true,
                    // @todo Create constraint for globally uniqueness of the slug
                    /*'constraints' => [
                        ['name' => 'unique', 'flags' => 'globally'],
                    ],*/
                ])
            );
        }
    }
}
