<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeDecoratorInterface
{
    public function decorate(ContentType $contentType): void;
}
