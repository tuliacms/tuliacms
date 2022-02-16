<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class NameSlugDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType(['node', 'taxonomy']) === false) {
            return;
        }

        $contentType->addField(new Field([
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

        if ($contentType->isRoutable()) {
            $contentType->addField(
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
