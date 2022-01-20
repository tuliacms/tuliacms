<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeDecoratorInterface;

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
            'name' => 'visibility',
            'type' => 'yes_no',
            'label' => 'visibility',
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
