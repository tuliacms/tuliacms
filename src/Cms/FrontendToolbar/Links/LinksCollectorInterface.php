<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Links;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface LinksCollectorInterface
{
    public function collect(LinksCollection $collection, Request $request): void;

    public function provideContent(Request $request): string;
}
