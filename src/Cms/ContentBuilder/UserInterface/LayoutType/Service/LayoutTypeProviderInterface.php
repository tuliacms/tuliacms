<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\LayoutType;

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
