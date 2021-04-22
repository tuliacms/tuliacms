<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Links;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface ProviderInterface
{
    public function provideLinks(Links $links, Request $request): void;
    public function provideContent(Request $request): string;
}