<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteProviderInterface
{
    public function provide(): array;
}
