<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class ParentTermDecorator implements ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('taxonomy') === false) {
            return;
        }

        if ($contentType->isHierarchical() === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'parent_id',
            'type' => 'taxonomy',
            'taxonomy' => $contentType->getCode(),
            'name' => 'parentTerm',
            'internal' => true,
        ]));
    }
}
