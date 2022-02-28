<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ContentBuilder;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FlagsDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'flags',
            'type' => 'node_purpose',
            'name' => 'nodePurpose',
            'is_multilingual' => false,
            'has_nonscalar_value' => true
        ]));
    }
}
