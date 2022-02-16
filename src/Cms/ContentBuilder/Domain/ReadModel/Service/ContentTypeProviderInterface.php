<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeProviderInterface
{
    /**
     * @return ContentType[]
     */
    public function provide(): array;
}
