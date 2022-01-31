<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class VisibilityDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('taxonomy') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'visibility',
            'name' => 'visibility',
            'type' => 'yes_no',
            'multilingual' => true,
            'internal' => true,
            'builder_options' => function () {
                return [
                    'constraints' => [
                        'required' => [],
                    ],
                ];
            }
        ]));
    }
}
