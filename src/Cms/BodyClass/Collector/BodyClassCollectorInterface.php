<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Collector;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface BodyClassCollectorInterface
{
    public function collect(Request $request, BodyClassCollection $collection): void;
}
