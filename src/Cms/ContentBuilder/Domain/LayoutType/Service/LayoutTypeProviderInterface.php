<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
interface LayoutTypeProviderInterface
{
    /**
     * @return LayoutType[]
     */
    public function provide(): array;
}
