<?php

declare(strict_types=1);

namespace Tulia\Cms\BodyClass\Ports\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\BodyClass\Domain\BodyClassCollection;

/**
 * @author Adam Banaszkiewicz
 */
interface BodyClassCollectorInterface
{
    public function collect(Request $request, BodyClassCollection $collection): void;
}
