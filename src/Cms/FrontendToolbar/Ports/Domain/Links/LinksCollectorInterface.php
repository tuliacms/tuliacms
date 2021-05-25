<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Ports\Domain\Links;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\FrontendToolbar\Domain\Links\LinksCollection;

/**
 * @author Adam Banaszkiewicz
 */
interface LinksCollectorInterface
{
    public function collect(LinksCollection $collection, Request $request): void;

    public function provideContent(Request $request): string;
}
