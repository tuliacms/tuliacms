<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;

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
