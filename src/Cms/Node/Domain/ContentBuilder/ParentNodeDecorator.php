<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ContentBuilder;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParentNodeDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        if ($contentType->isHierarchical() === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'parent_id',
            'type' => 'node_select',
            'name' => 'parentNode',
        ]));
    }
}
