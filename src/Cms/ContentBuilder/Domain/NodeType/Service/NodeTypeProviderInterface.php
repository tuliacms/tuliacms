<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeTypeProviderInterface
{
    /**
     * @return NodeType[]
     */
    public function provide(): array;
}
