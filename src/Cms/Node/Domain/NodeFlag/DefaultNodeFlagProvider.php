<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeFlag;

use Tulia\Cms\Node\Domain\NodeFlag\Enum\NodeFlagEnum;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultNodeFlagProvider implements NodeFlagProviderInterface
{
    public function provide(): array
    {
        return [
            NodeFlagEnum::PAGE_HOMEPAGE => [
                'singular' => true,
            ],
            NodeFlagEnum::PAGE_CONTACT => [
                'singular' => true,
            ],
            NodeFlagEnum::PAGE_PRIVACY_POLICY => [
                'singular' => true,
            ],
        ];
    }
}
