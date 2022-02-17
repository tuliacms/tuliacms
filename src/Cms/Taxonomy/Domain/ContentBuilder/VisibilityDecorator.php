<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ContentBuilder;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

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
            'is_multilingual' => true,
            'constraints' => [
                'required' => [],
            ],
        ]));
    }
}
